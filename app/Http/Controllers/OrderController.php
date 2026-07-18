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

            // Check if there is already an active (unpaid) order for this table
            $order = Order::where('table_id', $request->table_id)
                ->where('restaurant_id', $request->restaurant_id)
                ->where('payment_status', 'pending')
                ->first();

            if ($order) {
                // Update existing active order
                $order->total_amount = $totalAmount;
                $order->special_instructions = $request->special_instructions ?? $order->special_instructions;
                $order->save();
                // Delete old items for this order to replace with updated list
                $order->items()->delete();
            } else {
                // Create new order
                $order = Order::create([
                    'restaurant_id' => $request->restaurant_id,
                    'table_id' => $request->table_id,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'payment_method' => $request->payment_method ?? 'cash',
                    'special_instructions' => $request->special_instructions ?? '',
                ]);
            }

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

    public function showReceipt($id)
    {
        $order = Order::with('items')->find($id);

        if (!$order) {
            abort(404, 'Order not found');
        }

        $restaurant = \App\Models\Restaurant::find($order->restaurant_id) ?? new \App\Models\Restaurant([
            'name' => 'DineFlow AI POS House',
            'address' => '456 Gourmet Boulevard',
            'phone' => '555-0199'
        ]);

        $table = \App\Models\Table::find($order->table_id);
        $tableName = $table ? $table->table_number : 'Table #' . $order->table_id;

        return view('pos.receipt', compact('order', 'restaurant', 'tableName'));
    }

    public function showKitchenKOT($id)
    {
        $order = Order::with('items.menuItem')->find($id);
        if (!$order) {
            abort(404, 'Order not found');
        }

        $table = \App\Models\Table::find($order->table_id);
        $tableName = $table ? $table->table_number : 'Table #' . $order->table_id;

        $kitchenItems = $order->items->filter(function($item) {
            $cat = strtolower($item->menuItem?->category ?? '');
            return in_array($cat, ['mains', 'starters', 'breads', 'rice', 'pizza', 'burger', 'pasta', 'soup', 'main course']);
        });

        return view('pos.kot_kitchen', compact('order', 'kitchenItems', 'tableName'));
    }

    public function showBarKOT($id)
    {
        $order = Order::with('items.menuItem')->find($id);
        if (!$order) {
            abort(404, 'Order not found');
        }

        $table = \App\Models\Table::find($order->table_id);
        $tableName = $table ? $table->table_number : 'Table #' . $order->table_id;

        $barItems = $order->items->filter(function($item) {
            $cat = strtolower($item->menuItem?->category ?? '');
            return in_array($cat, ['beverages', 'desserts', 'drinks', 'dessert']);
        });

        return view('pos.kot_bar', compact('order', 'barItems', 'tableName'));
    }

    public function showCombinedKOT($id)
    {
        $order = Order::with('items.menuItem')->find($id);
        if (!$order) {
            abort(404, 'Order not found');
        }

        $table = \App\Models\Table::find($order->table_id);
        $tableName = $table ? $table->table_number : 'Table #' . $order->table_id;

        return view('pos.kot_combined', compact('order', 'tableName'));
    }
}
