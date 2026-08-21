<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Service class handling idempotent offline synchronization from POS desktop terminals
 * to central cloud database with conflict resolution.
 */
class OfflineSyncQueueService
{
    /**
     * Process an incoming synced order from offline SQLite client.
     *
     * @param array $payload Synced order payload
     * @return array Sync result status
     */
    public function syncIncomingOrder(array $payload): array
    {
        $clientOrderId = $payload['client_order_id'] ?? null;
        $restaurantId = (string)($payload['restaurant_id'] ?? '1');
        $tableId = (string)($payload['table_id'] ?? '1');
        $items = $payload['items'] ?? [];

        if (empty($items)) {
            return [
                'success' => false,
                'message' => 'No items provided in sync payload.',
            ];
        }

        return DB::transaction(function () use ($clientOrderId, $restaurantId, $tableId, $items, $payload) {
            // Idempotency check: prevent duplicate insertion
            if ($clientOrderId) {
                $existing = Order::where('restaurant_id', $restaurantId)
                    ->where('special_instructions', 'like', "%ClientSyncID:{$clientOrderId}%")
                    ->first();

                if ($existing) {
                    return [
                        'success' => true,
                        'message' => 'Order already synchronized (idempotent duplicate skipped).',
                        'order_id' => $existing->id,
                        'status' => $existing->status,
                    ];
                }
            }

            $subtotal = 0.00;
            foreach ($items as $item) {
                $subtotal += ((float)($item['price'] ?? 0) * (int)($item['quantity'] ?? 1));
            }

            $tax = round($subtotal * 0.05, 2);
            $service = round($subtotal * 0.05, 2);
            $total = $subtotal + $tax + $service;

            $customNotes = $payload['special_instructions'] ?? '';
            $syncStamp = $clientOrderId ? " | ClientSyncID:{$clientOrderId}" : '';

            $order = Order::create([
                'restaurant_id' => $restaurantId,
                'table_id' => $tableId,
                'order_type' => $payload['order_type'] ?? 'dine_in',
                'status' => $payload['status'] ?? 'pending',
                'payment_status' => $payload['payment_status'] ?? 'unpaid',
                'payment_method' => $payload['payment_method'] ?? 'cash',
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'service_charge' => $service,
                'total_amount' => $total,
                'special_instructions' => trim($customNotes . $syncStamp),
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'] ?? null,
                    'name' => $item['name'] ?? 'Custom Item',
                    'price' => (float)($item['price'] ?? 0),
                    'quantity' => (int)($item['quantity'] ?? 1),
                ]);
            }

            // Update table occupancy status
            Table::where('id', $tableId)
                ->where('restaurant_id', $restaurantId)
                ->update(['status' => 'occupied']);

            return [
                'success' => true,
                'message' => 'Offline order successfully synchronized.',
                'order_id' => $order->id,
                'total_amount' => $total,
            ];
        });
    }
}
