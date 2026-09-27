<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Inventory;
use App\Models\Table;
use App\Models\Restaurant;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;

class OrderService
{
    /**
     * Enforce strict tenant ownership.
     */
    public function assertTenantAccess(Order $order, ?User $user = null): void
    {
        $user = $user ?? auth()->user();
        if ($user && $user->role !== 'superadmin') {
            if ((int)$user->restaurant_id !== (int)$order->restaurant_id) {
                throw new AccessDeniedHttpException('Unauthorized tenant access to order #' . $order->id);
            }
        }
    }

    /**
     * Create or append to an order.
     */
    public function createOrder(array $data, ?User $user = null): Order
    {
        $user = $user ?? auth()->user();
        $restaurantId = $user ? (string)$user->restaurant_id : (string)($data['restaurant_id'] ?? '');

        if (!$restaurantId) {
            throw new ValidationException(validator([], []), ['restaurant_id' => ['Restaurant ID is required']]);
        }

        $restaurant = Restaurant::find($restaurantId);
        if (!$restaurant) {
            throw new NotFoundHttpException('Restaurant not found.');
        }

        if (!$restaurant->isOperational()) {
            throw new AccessDeniedHttpException('This restaurant is currently not accepting orders.');
        }

        $limitCheck = PlanLimitService::canCreateOrder($restaurantId);
        if (!$limitCheck['allowed']) {
            throw new AccessDeniedHttpException($limitCheck['message'] ?? 'Order limit exceeded for this subscription plan.');
        }

        $rawItems = $data['items'] ?? [];
        if (empty($rawItems) || !is_array($rawItems)) {
            throw new ValidationException(validator([], []), ['items' => ['At least one item is required']]);
        }

        return DB::transaction(function () use ($data, $restaurantId, $rawItems, $user) {
            $totalAmount = 0;
            $itemsData = [];

            foreach ($rawItems as $rawItem) {
                $menuItemId = $rawItem['menu_item_id'] ?? $rawItem['menuItemId'] ?? null;
                $quantity = max(1, intval($rawItem['quantity'] ?? $rawItem['qty'] ?? 1));

                if (!$menuItemId && !empty($rawItem['name'])) {
                    $menuItem = MenuItem::where('name', $rawItem['name'])
                        ->where('restaurant_id', $restaurantId)
                        ->first();
                } else {
                    $menuItem = MenuItem::where('restaurant_id', $restaurantId)->find($menuItemId);
                }

                if (!$menuItem) {
                    if (!empty($rawItem['name']) && isset($rawItem['price']) && floatval($rawItem['price']) > 0) {
                        $price = floatval($rawItem['price']);
                        $totalAmount += $price * $quantity;
                        $itemsData[] = [
                            'menu_item_id' => null,
                            'name' => (string)$rawItem['name'],
                            'quantity' => $quantity,
                            'price' => $price,
                        ];
                    }
                    continue;
                }

                // If marked sold-out, skip or reject
                if (!$menuItem->is_available) {
                    continue;
                }

                $price = (!empty($rawItem['price']) && floatval($rawItem['price']) >= floatval($menuItem->price))
                    ? floatval($rawItem['price'])
                    : floatval($menuItem->price);

                $totalAmount += $price * $quantity;
                $itemsData[] = [
                    'menu_item_id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'quantity' => $quantity,
                    'price' => $price,
                ];

                // Inventory decrement
                try {
                    $inventory = Inventory::where('restaurant_id', $restaurantId)
                        ->where('item_name', 'LIKE', '%' . $menuItem->name . '%')
                        ->first();
                    if ($inventory && $inventory->quantity >= $quantity) {
                        $inventory->decrement('quantity', $quantity);
                    }
                } catch (\Exception $e) {
                    Log::warning('Inventory deduction warning: ' . $e->getMessage());
                }
            }

            if (empty($itemsData)) {
                throw new ValidationException(validator([], []), ['items' => ['No available menu items could be ordered.']]);
            }

            $tableId = $data['table_id'] ?? '1';

            // Check if there is already an active (unpaid) order for this table
            $order = Order::where('table_id', $tableId)
                ->where('restaurant_id', $restaurantId)
                ->where('payment_status', 'pending')
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($order) {
                // Reset status to pending so kitchen sees new items
                $order->status = 'pending';
                if (!empty($data['special_instructions'])) {
                    $order->special_instructions = $order->special_instructions
                        ? $order->special_instructions . ' | ' . $data['special_instructions']
                        : $data['special_instructions'];
                }

                foreach ($itemsData as $newItem) {
                    $existingItem = $order->items()
                        ->where('name', $newItem['name'])
                        ->first();

                    if ($existingItem) {
                        $existingItem->quantity += $newItem['quantity'];
                        $existingItem->save();
                    } else {
                        $order->items()->create($newItem);
                    }
                }

                $this->recalculateOrderTotal($order);
                $order->save();

                AuditLog::record(
                    'item_added',
                    (int)$restaurantId,
                    'order',
                    $order->id,
                    ['added_items' => $itemsData, 'new_total' => $order->total_amount]
                );
            } else {
                $kotLang = KotLanguageService::getRestaurantLanguage($restaurantId);

                $order = Order::create([
                    'restaurant_id' => $restaurantId,
                    'table_id' => (string)$tableId,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'special_instructions' => $data['special_instructions'] ?? '',
                    'kot_language' => $kotLang,
                ]);

                foreach ($itemsData as $itemData) {
                    $order->items()->create($itemData);
                }

                // Mark table occupied if table exists
                if ($tableId && $tableId !== '0') {
                    $table = Table::where('restaurant_id', $restaurantId)->find($tableId)
                        ?: Table::where('restaurant_id', $restaurantId)->where('table_number', $tableId)->first();
                    if ($table && $table->status !== 'occupied') {
                        $table->update(['status' => 'occupied']);
                    }
                }

                AuditLog::record(
                    'order_created',
                    (int)$restaurantId,
                    'order',
                    $order->id,
                    ['total' => $totalAmount, 'table_id' => $tableId, 'items_count' => count($itemsData)]
                );
            }

            return $order->load(['items.menuItem', 'table']);
        });
    }

    /**
     * Update order metadata (table, instructions, payment_method).
     */
    public function updateOrder(Order $order, array $data, ?User $user = null): Order
    {
        $this->assertTenantAccess($order, $user);

        if ($order->payment_status === 'paid' && $order->status === 'completed') {
            $userRole = strtolower($user?->role ?? 'cashier');
            if (!in_array($userRole, ['admin', 'owner', 'manager'])) {
                throw new AccessDeniedHttpException('Only Admin or Manager can modify paid/finalized orders.');
            }
        }

        $oldTable = $order->table_id;
        $newTable = isset($data['table_id']) ? (string)$data['table_id'] : $oldTable;

        if (isset($data['table_id'])) {
            $order->table_id = $newTable;
        }
        if (isset($data['special_instructions'])) {
            $order->special_instructions = $data['special_instructions'];
        }
        if (isset($data['payment_method'])) {
            $order->payment_method = $data['payment_method'];
        }

        $order->save();

        // Switch table statuses if table changed
        if ($oldTable !== $newTable) {
            $this->releaseTableIfNoActiveOrders($order->restaurant_id, $oldTable);
            $this->occupyTable($order->restaurant_id, $newTable);
        }

        AuditLog::record(
            'order_updated',
            (int)$order->restaurant_id,
            'order',
            $order->id,
            ['changed_fields' => array_keys($data), 'old_table' => $oldTable, 'new_table' => $newTable]
        );

        return $order->load(['items.menuItem', 'table']);
    }

    /**
     * Add a single item to an existing order.
     */
    public function addItem(Order $order, array $itemData, ?User $user = null): OrderItem
    {
        $this->assertTenantAccess($order, $user);

        if ($order->status === 'cancelled') {
            throw new AccessDeniedHttpException('Cannot add items to a cancelled order.');
        }

        if ($order->payment_status === 'paid') {
            $userRole = strtolower($user?->role ?? 'cashier');
            if (!in_array($userRole, ['admin', 'owner', 'manager'])) {
                throw new AccessDeniedHttpException('Cannot add items to an already paid order without manager authorization.');
            }
        }

        return DB::transaction(function () use ($order, $itemData) {
            $menuItemId = $itemData['menu_item_id'] ?? null;
            $name = $itemData['name'] ?? null;
            $quantity = max(1, intval($itemData['quantity'] ?? 1));
            $price = floatval($itemData['price'] ?? 0);

            if ($menuItemId) {
                $menuItem = MenuItem::where('restaurant_id', $order->restaurant_id)->find($menuItemId);
                if ($menuItem) {
                    $name = $menuItem->name;
                    $price = $price > 0 ? $price : floatval($menuItem->price);
                }
            }

            if (!$name) {
                throw new ValidationException(validator([], []), ['name' => ['Item name is required']]);
            }

            $existingItem = $order->items()
                ->where(function ($q) use ($menuItemId, $name) {
                    if ($menuItemId) {
                        $q->where('menu_item_id', $menuItemId);
                    } else {
                        $q->where('name', $name);
                    }
                })->first();

            if ($existingItem) {
                $existingItem->quantity += $quantity;
                $existingItem->save();
                $orderItem = $existingItem;
            } else {
                $orderItem = $order->items()->create([
                    'menu_item_id' => $menuItemId,
                    'name' => $name,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);
            }

            // If order was marked served/ready, set back to pending so kitchen sees new item
            if (in_array($order->status, ['served', 'ready'])) {
                $order->status = 'pending';
            }

            $this->recalculateOrderTotal($order);
            $order->save();

            AuditLog::record(
                'item_added',
                (int)$order->restaurant_id,
                'order',
                $order->id,
                ['item_name' => $name, 'quantity' => $quantity, 'price' => $price]
            );

            return $orderItem;
        });
    }

    /**
     * Update quantity or details of an existing item in an order.
     */
    public function updateItem(Order $order, $itemId, array $data, ?User $user = null): OrderItem
    {
        $this->assertTenantAccess($order, $user);

        if ($order->status === 'cancelled') {
            throw new AccessDeniedHttpException('Cannot edit items on a cancelled order.');
        }

        if ($order->payment_status === 'paid') {
            $userRole = strtolower($user?->role ?? 'cashier');
            if (!in_array($userRole, ['admin', 'owner', 'manager'])) {
                throw new AccessDeniedHttpException('Only Admin or Manager can edit items on paid orders.');
            }
        }

        $orderItem = $order->items()->findOrFail($itemId);

        return DB::transaction(function () use ($order, $orderItem, $data) {
            $oldQty = $orderItem->quantity;
            $newQty = isset($data['quantity']) ? intval($data['quantity']) : $oldQty;

            if ($newQty <= 0) {
                $this->removeItem($order, $orderItem->id);
                return $orderItem;
            }

            $orderItem->quantity = $newQty;
            if (isset($data['price']) && floatval($data['price']) >= 0) {
                $orderItem->price = floatval($data['price']);
            }
            if (isset($data['name']) && !empty($data['name'])) {
                $orderItem->name = $data['name'];
            }
            $orderItem->save();

            $this->recalculateOrderTotal($order);
            $order->save();

            AuditLog::record(
                'item_quantity_changed',
                (int)$order->restaurant_id,
                'order',
                $order->id,
                ['item_name' => $orderItem->name, 'old_qty' => $oldQty, 'new_qty' => $newQty]
            );

            return $orderItem;
        });
    }

    /**
     * Remove an item from an order.
     */
    public function removeItem(Order $order, $itemId, ?User $user = null): void
    {
        $this->assertTenantAccess($order, $user);

        if ($order->payment_status === 'paid') {
            $userRole = strtolower($user?->role ?? 'cashier');
            if (!in_array($userRole, ['admin', 'owner', 'manager'])) {
                throw new AccessDeniedHttpException('Only Admin or Manager can remove items from a paid order.');
            }
        }

        $orderItem = $order->items()->findOrFail($itemId);

        DB::transaction(function () use ($order, $orderItem) {
            $itemName = $orderItem->name;
            $itemQty = $orderItem->quantity;
            $orderItem->delete();

            $remainingCount = $order->items()->count();
            if ($remainingCount === 0) {
                // If 0 items remain, auto-cancel order
                $order->status = 'cancelled';
                $order->total_amount = 0;
                $order->save();
                $this->releaseTableIfNoActiveOrders($order->restaurant_id, $order->table_id);
            } else {
                $this->recalculateOrderTotal($order);
                $order->save();
            }

            AuditLog::record(
                'item_removed',
                (int)$order->restaurant_id,
                'order',
                $order->id,
                ['item_name' => $itemName, 'quantity' => $itemQty, 'remaining_items' => $remainingCount]
            );
        });
    }

    /**
     * Cancel an order with required reason.
     */
    public function cancelOrder(Order $order, string $reason = 'Customer Cancelled', ?User $user = null): Order
    {
        $this->assertTenantAccess($order, $user);

        if ($order->status === 'cancelled') {
            return $order;
        }

        $userRole = strtolower($user?->role ?? 'cashier');

        // Paid order cancellation requires manager or admin
        if ($order->payment_status === 'paid') {
            if (!in_array($userRole, ['admin', 'owner', 'manager'])) {
                throw new AccessDeniedHttpException('Cancelling an already paid order requires Admin or Manager authorization.');
            }
        }

        DB::transaction(function () use ($order, $reason, $userRole) {
            $oldStatus = $order->status;
            $order->status = 'cancelled';

            $notePrefix = "[CANCELLED by {$userRole} - Reason: {$reason}]";
            $order->special_instructions = $order->special_instructions
                ? "{$notePrefix} " . $order->special_instructions
                : $notePrefix;

            $order->save();

            // Release table if no other active orders on table
            $this->releaseTableIfNoActiveOrders($order->restaurant_id, $order->table_id);

            AuditLog::record(
                'order_cancelled',
                (int)$order->restaurant_id,
                'order',
                $order->id,
                ['reason' => $reason, 'previous_status' => $oldStatus, 'was_paid' => ($order->payment_status === 'paid')]
            );
        });

        return $order->load(['items.menuItem', 'table']);
    }

    /**
     * Update order workflow status (pending -> preparing -> ready -> served -> completed).
     */
    public function updateStatus(Order $order, string $newStatus, ?User $user = null): Order
    {
        $this->assertTenantAccess($order, $user);

        $allowedStatuses = ['pending', 'preparing', 'cooking', 'ready', 'served', 'completed', 'cancelled'];
        if (!in_array($newStatus, $allowedStatuses)) {
            throw new ValidationException(validator([], []), ['status' => ['Invalid order status.']]);
        }

        if ($newStatus === 'cancelled') {
            return $this->cancelOrder($order, 'Status changed to cancelled', $user);
        }

        $oldStatus = $order->status;
        $order->status = $newStatus === 'cooking' ? 'preparing' : $newStatus;
        $order->save();

        if (in_array($order->status, ['completed', 'served']) && $order->payment_status === 'paid') {
            $this->releaseTableIfNoActiveOrders($order->restaurant_id, $order->table_id);
        }

        AuditLog::record(
            'status_changed',
            (int)$order->restaurant_id,
            'order',
            $order->id,
            ['old_status' => $oldStatus, 'new_status' => $order->status]
        );

        return $order->load(['items.menuItem', 'table']);
    }

    /**
     * Process payment for an order.
     */
    public function processPayment(Order $order, string $paymentMethod = 'cash', ?User $user = null, ?string $transactionRef = null): Order
    {
        $this->assertTenantAccess($order, $user);

        if ($order->status === 'cancelled') {
            throw new AccessDeniedHttpException('Cannot pay for a cancelled order.');
        }

        DB::transaction(function () use ($order, $paymentMethod, $transactionRef) {
            $order->payment_status = 'paid';
            $order->payment_method = $paymentMethod;
            $order->status = 'completed';
            $order->save();

            $this->releaseTableIfNoActiveOrders($order->restaurant_id, $order->table_id);

            AuditLog::record(
                'payment_recorded',
                (int)$order->restaurant_id,
                'order',
                $order->id,
                ['payment_method' => $paymentMethod, 'amount' => $order->total_amount, 'transaction_ref' => $transactionRef]
            );
        });

        return $order->load(['items.menuItem', 'table']);
    }

    /**
     * Safe delete order rules.
     * Paid/completed orders CANNOT be deleted.
     */
    public function deleteOrder(Order $order, ?User $user = null): void
    {
        $this->assertTenantAccess($order, $user);

        $userRole = strtolower($user?->role ?? 'cashier');
        if (!in_array($userRole, ['admin', 'owner'])) {
            throw new AccessDeniedHttpException('Only Admin or Restaurant Owner can permanently delete order records.');
        }

        if ($order->payment_status === 'paid' || $order->status === 'completed') {
            throw new AccessDeniedHttpException('Database Safety Rule: Cannot delete paid or completed orders. Historical financial records must be preserved. Use Cancel or Void instead.');
        }

        DB::transaction(function () use ($order) {
            $orderId = $order->id;
            $restId = $order->restaurant_id;
            $tableId = $order->table_id;

            $order->items()->delete();
            $order->delete();

            $this->releaseTableIfNoActiveOrders($restId, $tableId);

            AuditLog::record(
                'order_deleted',
                (int)$restId,
                'order',
                $orderId,
                ['deleted_order_id' => $orderId, 'table_id' => $tableId]
            );
        });
    }

    /**
     * Recalculate and update the order total_amount from its items.
     */
    public function recalculateOrderTotal(Order $order): float
    {
        $total = 0;
        foreach ($order->items()->get() as $item) {
            $total += floatval($item->price) * intval($item->quantity);
        }
        $order->total_amount = round($total, 2);
        return $order->total_amount;
    }

    /**
     * Calculate comprehensive bill totals (GST, service charge, grand total).
     */
    public function calculateBillBreakdown(Order $order, bool $applyServiceCharge = true): array
    {
        $subtotal = floatval($order->total_amount);
        $tax = round($subtotal * 0.05, 2); // 5% GST
        $service = $applyServiceCharge ? round($subtotal * 0.05, 2) : 0.00;
        $grandTotal = round($subtotal + $tax + $service, 2);

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'service_charge' => $service,
            'grand_total' => $grandTotal,
            'rounded_total' => (int)round($grandTotal),
        ];
    }

    /**
     * Get audit log history for an order.
     */
    public function getOrderHistory(Order $order)
    {
        return AuditLog::where('target_type', 'order')
            ->where('target_id', $order->id)
            ->where('restaurant_id', $order->restaurant_id)
            ->with('user:id,name,role')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Occupy a table.
     */
    protected function occupyTable(string $restaurantId, string $tableId): void
    {
        if (!$tableId || $tableId === '0') return;
        $table = Table::where('restaurant_id', $restaurantId)->find($tableId)
            ?: Table::where('restaurant_id', $restaurantId)->where('table_number', $tableId)->first();
        if ($table && $table->status !== 'occupied') {
            $table->update(['status' => 'occupied']);
        }
    }

    /**
     * Release a table if no active unpaid orders remain.
     */
    protected function releaseTableIfNoActiveOrders(string $restaurantId, string $tableId): void
    {
        if (!$tableId || $tableId === '0') return;

        $hasActive = Order::where('restaurant_id', $restaurantId)
            ->where('table_id', $tableId)
            ->where('payment_status', 'pending')
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->exists();

        if (!$hasActive) {
            $table = Table::where('restaurant_id', $restaurantId)->find($tableId)
                ?: Table::where('restaurant_id', $restaurantId)->where('table_number', $tableId)->first();
            if ($table && $table->status === 'occupied') {
                $table->update(['status' => 'available']);
            }
        }
    }
}
