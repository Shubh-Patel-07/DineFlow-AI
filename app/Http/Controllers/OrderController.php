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
                // Reset status to pending so new/appended items reappear on the kitchen display!
                $order->status = 'pending';
                $order->special_instructions = $request->special_instructions ?? $order->special_instructions;
                
                // Merge/append items
                foreach ($itemsData as $newItem) {
                    $existingItem = $order->items()->where('menu_item_id', $newItem['menu_item_id'])->first();
                    if ($existingItem) {
                        $existingItem->quantity += $newItem['quantity'];
                        $existingItem->save();
                    } else {
                        $order->items()->create($newItem);
                    }
                }

                // Recalculate total amount for all items in the order
                $newTotal = 0;
                foreach ($order->items()->get() as $item) {
                    $newTotal += $item->price * $item->quantity;
                }
                $order->total_amount = $newTotal;
                $order->save();
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

                foreach ($itemsData as $itemData) {
                    $order->items()->create($itemData);
                }
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
            $order = new Order([
                'id' => is_numeric($id) ? $id : 9,
                'restaurant_id' => 1,
                'table_id' => 1,
                'total_amount' => 920,
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => 'cash',
                'created_at' => now(),
            ]);
            $order->setRelation('items', collect([
                (object)['name' => 'Chicken Biryani', 'quantity' => 1, 'price' => 380],
                (object)['name' => 'Mango Lassi', 'quantity' => 1, 'price' => 120],
                (object)['name' => 'Veg Biryani', 'quantity' => 1, 'price' => 280],
                (object)['name' => 'Cold Coffee', 'quantity' => 1, 'price' => 140],
            ]));
        }

        $restaurant = \App\Models\Restaurant::find($order->restaurant_id) ?? new \App\Models\Restaurant([
            'name' => 'ULTIMATE DINEFLOW AI',
            'address' => '456 Gourmet Boulevard, Patan',
            'phone' => '+91 98765 43210'
        ]);

        $table = \App\Models\Table::find($order->table_id);
        $tableName = $table ? $table->table_number : 'Table #' . $order->table_id;

        return view('pos.receipt', compact('order', 'restaurant', 'tableName'));
    }

    public function showA4Invoice(Request $request, $id)
    {
        $order = null;
        if (is_numeric($id) && $id > 0) {
            $order = Order::with('items.menuItem')->find($id);
        }

        if ($id === 'live' || $request->has('items') || !$order) {
            $itemsData = json_decode($request->get('items', '[]'), true);
            $tableName = $request->get('table', 'Walk-In');
            $calcSubtotal = 0;
            $itemsCollection = collect();

            if (is_array($itemsData) && count($itemsData) > 0) {
                foreach ($itemsData as $it) {
                    $qty = (int)($it['qty'] ?? $it['quantity'] ?? 1);
                    $price = (float)($it['price'] ?? $it['unit_price'] ?? 0);
                    $calcSubtotal += $price * $qty;
                    $itemsCollection->push((object)[
                        'name' => $it['name'] ?? 'Item',
                        'quantity' => $qty,
                        'price' => $price,
                    ]);
                }
            } else if (!$order) {
                $calcSubtotal = 360;
                $itemsCollection = collect([
                    (object)['name' => 'Paneer Butter Masala', 'quantity' => 1, 'price' => 240],
                    (object)['name' => 'Garlic Naan', 'quantity' => 2, 'price' => 60],
                ]);
            }

            if (!$order) {
                $order = new Order([
                    'id' => is_numeric($id) ? $id : time(),
                    'restaurant_id' => 1,
                    'table_id' => 1,
                    'total_amount' => $calcSubtotal,
                    'status' => 'placed',
                    'payment_status' => 'pending',
                    'payment_method' => 'cash',
                    'created_at' => now(),
                ]);
                $order->setRelation('items', $itemsCollection);
            }
        }

        $restaurant = \App\Models\Restaurant::find($order->restaurant_id) ?? new \App\Models\Restaurant([
            'name' => 'DineFlow AI Restaurant Suite',
            'address' => '456 Gourmet Boulevard, Commercial Hub, Patan, Gujarat - 384265',
            'phone' => '+91 98765 43210',
            'email' => 'billing@dineflow.ai',
            'gstin' => '24ABCDE1234F1Z5'
        ]);

        $table = \App\Models\Table::find($order->table_id);
        $tableName = isset($tableName) ? $tableName : ($table ? $table->table_number : 'Table #' . $order->table_id);

        return view('pos.invoice_a4', compact('order', 'restaurant', 'tableName'));
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
