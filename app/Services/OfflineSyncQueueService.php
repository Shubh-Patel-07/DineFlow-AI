<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Inventory;
use App\Models\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

/**
 * Service class handling idempotent offline synchronization between POS desktop terminals
 * and central cloud database with conflict resolution and tenant boundary isolation.
 */
class OfflineSyncQueueService
{
    /**
     * Monotonic status order hierarchy to prevent regression
     */
    protected const STATUS_HIERARCHY = [
        'pending'   => 1,
        'preparing' => 2,
        'ready'     => 3,
        'served'    => 4,
        'completed' => 5,
        'cancelled' => 6,
    ];

    /**
     * Process an incoming synced order from offline SQLite client.
     *
     * @param array $payload Synced order payload
     * @param string|int|null $authenticatedRestaurantId Authoritative tenant ID from auth context
     * @return array Sync result status
     */
    public function syncIncomingOrder(array $payload, $authenticatedRestaurantId = null): array
    {
        // Enforce authenticated tenant ID over client-supplied value
        $restaurantId = (string)($authenticatedRestaurantId ?? $payload['restaurant_id'] ?? '');
        if (empty($restaurantId)) {
            return [
                'success' => false,
                'message' => 'Restaurant ID is required for synchronization.',
            ];
        }

        $restaurant = \App\Models\Restaurant::find($restaurantId);
        if ($restaurant && $restaurant->is_demo) {
            return [
                'success' => false,
                'message' => 'Demo sandboxes cannot sync offline orders to production cloud.',
            ];
        }

        $clientOrderId = trim((string)($payload['client_order_id'] ?? $payload['order_uuid'] ?? ''));
        $tableId = (string)($payload['table_id'] ?? '1');
        $items = $payload['items'] ?? [];

        if (empty($items)) {
            return [
                'success' => false,
                'message' => 'No items provided in sync payload.',
            ];
        }

        return DB::transaction(function () use ($clientOrderId, $restaurantId, $tableId, $items, $payload) {
            // 1. Idempotency Check: Prevent duplicate order insertion
            if (!empty($clientOrderId)) {
                $existing = Order::where('restaurant_id', $restaurantId)
                    ->where('special_instructions', 'like', "%ClientSyncID:{$clientOrderId}%")
                    ->with('items')
                    ->first();

                if ($existing) {
                    return [
                        'success' => true,
                        'idempotent' => true,
                        'message' => 'Order already synchronized (idempotent duplicate skipped).',
                        'order_id' => $existing->id,
                        'status' => $existing->status,
                        'total_amount' => (float)$existing->total_amount,
                        'data' => $existing,
                    ];
                }
            }

            // 2. Server-Authoritative Price Calculation & Item Validation
            $subtotal = 0.00;
            $itemsData = [];

            foreach ($items as $item) {
                $menuItemId = $item['menu_item_id'] ?? $item['menuItemId'] ?? null;
                $quantity = max(1, intval($item['quantity'] ?? 1));
                $menuItem = null;

                if ($menuItemId) {
                    $menuItem = MenuItem::where('restaurant_id', $restaurantId)->find($menuItemId);
                }
                if (!$menuItem && !empty($item['name'])) {
                    $menuItem = MenuItem::where('restaurant_id', $restaurantId)->where('name', $item['name'])->first();
                }

                // Authoritative price resolution
                if ($menuItem) {
                    $price = (float)$menuItem->price;
                    $name = $menuItem->name;
                    $verifiedId = $menuItem->id;
                } else {
                    $price = max(0.00, (float)($item['price'] ?? 0.00));
                    $name = (string)($item['name'] ?? 'Custom Item');
                    $verifiedId = null;
                }

                $subtotal += ($price * $quantity);
                $itemsData[] = [
                    'menu_item_id' => $verifiedId,
                    'name'         => $name,
                    'price'        => $price,
                    'quantity'     => $quantity,
                ];

                // 3. Atomically deduct inventory for tracked items (on initial sync only)
                if ($menuItem) {
                    try {
                        $inventory = Inventory::where('restaurant_id', $restaurantId)
                            ->where('item_name', 'LIKE', '%' . $menuItem->name . '%')
                            ->first();

                        if ($inventory && $inventory->quantity >= $quantity) {
                            $inventory->decrement('quantity', $quantity);
                        }
                    } catch (Exception $e) {
                        Log::warning("Inventory sync error for {$name}: " . $e->getMessage());
                    }
                }
            }

            // 4. Authoritative Tax & Totals Calculation (5% GST, 5% Service Charge)
            $tax = round($subtotal * 0.05, 2);
            $service = round($subtotal * 0.05, 2);
            $total = $subtotal + $tax + $service;

            $customNotes = $payload['special_instructions'] ?? '';
            $syncStamp = $clientOrderId ? " | ClientSyncID:{$clientOrderId}" : '';

            // 5. Create Central Cloud Order Record
            $order = Order::create([
                'restaurant_id'        => $restaurantId,
                'table_id'             => $tableId,
                'order_type'           => $payload['order_type'] ?? 'dine_in',
                'status'               => $payload['status'] ?? 'pending',
                'payment_status'       => $payload['payment_status'] ?? 'pending',
                'payment_method'       => $payload['payment_method'] ?? 'cash',
                'subtotal'             => $subtotal,
                'tax_amount'           => $tax,
                'service_charge'       => $service,
                'total_amount'         => $total,
                'special_instructions' => trim($customNotes . $syncStamp),
            ]);

            foreach ($itemsData as $itemData) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'menu_item_id' => $itemData['menu_item_id'],
                    'name'         => $itemData['name'],
                    'price'        => $itemData['price'],
                    'quantity'     => $itemData['quantity'],
                ]);
            }

            // 6. Update Table Occupancy
            Table::where('id', $tableId)
                ->where('restaurant_id', $restaurantId)
                ->update(['status' => 'occupied']);

            return [
                'success'      => true,
                'idempotent'   => false,
                'message'      => 'Offline order successfully synchronized.',
                'order_id'     => $order->id,
                'total_amount' => $total,
                'data'         => $order->load('items'),
            ];
        });
    }

    /**
     * Fetch orders updated in cloud since a given timestamp for desktop reconciliation.
     *
     * @param string|int $restaurantId
     * @param string|null $sinceTimestamp
     * @param int $limit
     * @return array
     */
    public function getOrdersSince($restaurantId, ?string $sinceTimestamp = null, int $limit = 50): array
    {
        $query = Order::where('restaurant_id', $restaurantId)
            ->with(['items.menuItem', 'table'])
            ->orderBy('updated_at', 'asc');

        if (!empty($sinceTimestamp)) {
            try {
                $sinceDate = Carbon::parse($sinceTimestamp);
                $query->where('updated_at', '>', $sinceDate);
            } catch (Exception $e) {
                // If invalid timestamp, fallback to last 24 hours
                $query->where('updated_at', '>', Carbon::now()->subDay());
            }
        } else {
            // Default to today's orders
            $query->whereDate('created_at', Carbon::today());
        }

        $orders = $query->limit($limit)->get();

        return [
            'success' => true,
            'count'   => $orders->count(),
            'cursor'  => $orders->last() ? $orders->last()->updated_at->toISOString() : $sinceTimestamp,
            'data'    => $orders,
        ];
    }

    /**
     * Monotonic status update (rejects status regression from offline devices).
     *
     * @param string|int $restaurantId
     * @param int $orderId
     * @param string $newStatus
     * @return array
     */
    public function updateOrderStatusMonotonic($restaurantId, int $orderId, string $newStatus): array
    {
        $order = Order::where('restaurant_id', $restaurantId)->find($orderId);
        if (!$order) {
            return ['success' => false, 'message' => 'Order not found in restaurant.'];
        }

        $currentRank = self::STATUS_HIERARCHY[$order->status] ?? 1;
        $newRank = self::STATUS_HIERARCHY[$newStatus] ?? 1;

        if ($newRank < $currentRank) {
            return [
                'success' => false,
                'conflict' => true,
                'message' => "Cannot regress order status from '{$order->status}' to '{$newStatus}'.",
                'current_status' => $order->status,
            ];
        }

        $order->status = $newStatus;
        $order->save();

        return [
            'success' => true,
            'message' => "Order status updated to '{$newStatus}'.",
            'order'   => $order,
        ];
    }
}
