<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRider;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DeliveryAggregatorController extends Controller
{
    /**
     * Get list of delivery riders.
     */
    public function getRiders(Request $request)
    {
        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        $riders = DeliveryRider::where('restaurant_id', $restaurantId)->get();

        // Seed default starter riders if none exist
        if ($riders->isEmpty()) {
            $defaultRiders = [
                ['restaurant_id' => $restaurantId, 'name' => 'Ramesh Patel', 'phone' => '9876543210', 'vehicle_number' => 'GJ-01-AB-1234', 'status' => 'available'],
                ['restaurant_id' => $restaurantId, 'name' => 'Suresh Kumar', 'phone' => '9876543211', 'vehicle_number' => 'GJ-01-CD-5678', 'status' => 'available'],
            ];
            foreach ($defaultRiders as $r) {
                DeliveryRider::create($r);
            }
            $riders = DeliveryRider::where('restaurant_id', $restaurantId)->get();
        }

        return response()->json(['success' => true, 'data' => $riders]);
    }

    /**
     * Add a new delivery rider.
     */
    public function storeRider(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
        ]);

        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        $rider = DeliveryRider::create([
            'restaurant_id' => $restaurantId,
            'name' => $request->name,
            'phone' => $request->phone,
            'vehicle_number' => $request->vehicle_number ?: 'N/A',
            'status' => 'available',
        ]);

        AuditLog::record(
            'rider_created',
            (int)$restaurantId,
            'delivery_rider',
            $rider->id,
            ['name' => $rider->name, 'phone' => $rider->phone]
        );

        return response()->json([
            'success' => true,
            'message' => "Rider '{$rider->name}' added successfully.",
            'data' => $rider
        ], 201);
    }

    /**
     * Assign order to a delivery rider.
     */
    public function assignRider(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'rider_id' => 'required',
        ]);

        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        $order = Order::where('restaurant_id', $restaurantId)->find($request->order_id);
        $rider = DeliveryRider::where('restaurant_id', $restaurantId)->find($request->rider_id);

        if (!$order || !$rider) {
            return response()->json(['success' => false, 'message' => 'Order or Rider not found.'], 404);
        }

        $order->update([
            'rider_id' => $rider->id,
            'delivery_status' => 'out_for_delivery',
        ]);

        $rider->increment('active_orders_count');
        $rider->update(['status' => 'on_delivery']);

        AuditLog::record(
            'order_assigned_rider',
            (int)$restaurantId,
            'order',
            $order->id,
            ['rider_name' => $rider->name, 'rider_id' => $rider->id]
        );

        return response()->json([
            'success' => true,
            'message' => "Order #{$order->id} assigned to {$rider->name}. Status: Out for delivery 🛵",
            'data' => $order
        ]);
    }

    /**
     * Mark delivery completed and settle order.
     */
    public function markDelivered(Request $request, $orderId)
    {
        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        $order = Order::where('restaurant_id', $restaurantId)->find($orderId);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $order->update([
            'delivery_status' => 'delivered',
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        if ($order->rider_id) {
            $rider = DeliveryRider::find($order->rider_id);
            if ($rider) {
                $rider->decrement('active_orders_count', 1);
                $rider->increment('total_deliveries', 1);
                if ($rider->active_orders_count <= 0) {
                    $rider->update(['status' => 'available']);
                }
            }
        }

        AuditLog::record(
            'order_delivered',
            (int)$restaurantId,
            'order',
            $order->id,
            ['rider_id' => $order->rider_id, 'amount' => $order->total_amount]
        );

        return response()->json([
            'success' => true,
            'message' => "Order #{$order->id} marked Delivered & Settled successfully ✅",
            'data' => $order
        ]);
    }

    /**
     * Get Live Zomato & Swiggy online orders.
     */
    public function getAggregatorOrders(Request $request)
    {
        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        $orders = Order::where('restaurant_id', $restaurantId)
            ->whereIn('order_type', ['zomato', 'swiggy', 'delivery'])
            ->with(['items', 'rider'])
            ->latest()
            ->take(30)
            ->get();

        $pendingCount = $orders->whereIn('status', ['pending', 'preparing'])->count();

        return response()->json([
            'success' => true,
            'pending_count' => $pendingCount,
            'data' => $orders
        ]);
    }

    /**
     * Simulate an incoming Zomato / Swiggy online order (with siren sound alert).
     */
    public function simulateOnlineOrder(Request $request)
    {
        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->restaurant_id ?? null);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized restaurant.'], 403);
        }

        $platform = strtolower($request->platform ?: (rand(0, 1) ? 'zomato' : 'swiggy'));
        $aggCode = strtoupper(substr($platform, 0, 3)) . '-' . rand(1000, 9999);

        // Pick 1-2 menu items
        $menuItems = MenuItem::where('restaurant_id', $restaurantId)->take(2)->get();
        if ($menuItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No menu items exist to simulate order.'], 400);
        }

        $subtotal = 0;
        $itemsData = [];
        foreach ($menuItems as $m) {
            $qty = rand(1, 2);
            $subtotal += floatval($m->price) * $qty;
            $itemsData[] = [
                'menu_item_id' => $m->id,
                'name' => $m->name,
                'price' => floatval($m->price),
                'quantity' => $qty,
                'notes' => 'Online Packaged',
                'kot_status' => 'pending'
            ];
        }

        $order = Order::create([
            'restaurant_id' => $restaurantId,
            'table_id' => '0',
            'order_type' => $platform,
            'total_amount' => $subtotal,
            'status' => 'pending',
            'payment_status' => 'paid', // Online aggregator orders are prepaid
            'payment_method' => $platform,
            'customer_name' => 'Online Customer (' . ucfirst($platform) . ')',
            'customer_phone' => '98980' . rand(10000, 99999),
            'customer_address' => 'Flat 402, Shivalik Residency, Ahmedabad',
            'aggregator_order_id' => $aggCode,
            'delivery_status' => 'pending',
            'prep_time_minutes' => 20,
            'special_instructions' => 'Please provide extra cutlery & tissue napkins',
        ]);

        foreach ($itemsData as $item) {
            $order->items()->create($item);
        }

        AuditLog::record(
            'online_order_received',
            (int)$restaurantId,
            'order',
            $order->id,
            ['platform' => $platform, 'aggregator_id' => $aggCode, 'total' => $subtotal]
        );

        return response()->json([
            'success' => true,
            'message' => "🔔 Incoming {$platform} order #{$aggCode} received!",
            'data' => $order->load('items')
        ]);
    }

    /**
     * Accept online order and set prep time.
     */
    public function acceptOrder(Request $request, $orderId)
    {
        $prepTime = max(5, intval($request->prep_time ?: 20));
        $order = Order::findOrFail($orderId);

        $order->update([
            'status' => 'preparing',
            'prep_time_minutes' => $prepTime,
        ]);

        AuditLog::record(
            'online_order_accepted',
            (int)$order->restaurant_id,
            'order',
            $order->id,
            ['prep_time' => $prepTime]
        );

        return response()->json([
            'success' => true,
            'message' => "Order #{$order->aggregator_order_id} accepted! Prep time: {$prepTime} mins ⏱️",
            'data' => $order
        ]);
    }

    /**
     * Mark food ready for rider pickup.
     */
    public function markFoodReady(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        $order->update([
            'status' => 'ready',
        ]);

        AuditLog::record(
            'food_ready_for_pickup',
            (int)$order->restaurant_id,
            'order',
            $order->id,
            ['aggregator_id' => $order->aggregator_order_id]
        );

        return response()->json([
            'success' => true,
            'message' => "Food Ready! Aggregator notified for rider pickup 🛵",
            'data' => $order
        ]);
    }

    /**
     * Reject online order with reason.
     */
    public function rejectOrder(Request $request, $orderId)
    {
        $reason = $request->reason ?: 'Restaurant Kitchen Busy';
        $order = Order::findOrFail($orderId);

        $order->update([
            'status' => 'cancelled',
            'special_instructions' => 'Rejected: ' . $reason,
        ]);

        AuditLog::record(
            'online_order_rejected',
            (int)$order->restaurant_id,
            'order',
            $order->id,
            ['reason' => $reason]
        );

        return response()->json([
            'success' => true,
            'message' => "Order #{$order->aggregator_order_id} rejected.",
            'data' => $order
        ]);
    }
}
