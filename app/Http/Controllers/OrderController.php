<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|string',
            'table_id' => 'required|string',
            'items' => 'required|array',
        ]);

        return DB::transaction(function () use ($request) {
            $totalAmount = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $menuItemId = $item['menu_item_id'];
                $quantity = $item['quantity'];

                $menuItem = MenuItem::find($menuItemId);

                if (!$menuItem) {
                    return response()->json(['success' => false, 'message' => "Menu item {$menuItemId} not found"], 404);
                }

                $price = $menuItem->price;
                $totalAmount += $price * $quantity;

                $itemsData[] = [
                    'menu_item_id' => $menuItemId,
                    'name' => $menuItem->name,
                    'quantity' => $quantity,
                    'price' => $price,
                ];

                $inventory = Inventory::where('restaurant_id', $request->restaurant_id)
                    ->where('name', 'LIKE', $menuItem->name)
                    ->first();

                if ($inventory) {
                    $inventory->quantity = max(0, $inventory->quantity - $quantity);
                    $inventory->save();
                }
            }

            $order = Order::create([
                'restaurant_id' => $request->restaurant_id,
                'table_id' => $request->table_id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method ?? 'cash',
                'special_instructions' => $request->special_instructions ?? '',
            ]);

            foreach ($itemsData as $itemData) {
                $order->items()->create($itemData);
            }

            return response()->json(['success' => true, 'data' => $order->load('items')], 201);
        });
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,served,completed,cancelled',
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        $order->status = $request->status;
        $order->save();

        return response()->json(['success' => true, 'data' => $order]);
    }

    public function pay(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        $order->payment_status = 'paid';
        $order->payment_method = $request->payment_method ?? 'online';
        $order->save();

        return response()->json(['success' => true, 'data' => $order]);
    }

    public function getRestaurantOrders($restaurantId)
    {
        $orders = Order::where('restaurant_id', $restaurantId)->with('items')->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $orders]);
    }

    public function show($id)
    {
        $order = Order::with('items')->find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $order]);
    }
}
