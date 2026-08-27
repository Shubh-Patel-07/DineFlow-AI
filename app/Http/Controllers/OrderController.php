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
    public function index(Request $request)
    {
        $user = auth()->user();
        $restaurantId = $user ? $user->restaurant_id : ($request->query('restaurant_id') ?? $request->query('restaurantId'));
        if (!$restaurantId) {
            return response()->json(['success' => true, 'data' => []]);
        }
        $orders = Order::where('restaurant_id', $restaurantId)
            ->with(['items.menuItem', 'table'])
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['success' => true, 'data' => $orders]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required',
            'table_id' => 'required',
            'items' => 'required|array|min:1',
            'payment_method' => 'nullable|string',
            'special_instructions' => 'nullable|string',
        ]);

        $limitCheck = \App\Services\PlanLimitService::canCreateOrder($request->restaurant_id);
        if (!$limitCheck['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $limitCheck['message'],
                'limit_exceeded' => true,
                'details' => $limitCheck
            ], 422);
        }

        return DB::transaction(function () use ($request) {
            $totalAmount = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $menuItemId = $item['menu_item_id'] ?? $item['menuItemId'] ?? null;
                $quantity = max(1, intval($item['quantity'] ?? 1));

                if (!$menuItemId && !empty($item['name'])) {
                    $menuItem = MenuItem::where('name', $item['name'])->first();
                } else {
                    $menuItem = MenuItem::find($menuItemId);
                }

                if (!$menuItem) {
                    if (!empty($item['name']) && isset($item['price']) && floatval($item['price']) > 0) {
                        $price = floatval($item['price']);
                        $totalAmount += $price * $quantity;
                        $itemsData[] = [
                            'menu_item_id' => null,
                            'name' => (string)$item['name'],
                            'quantity' => $quantity,
                            'price' => $price,
                        ];
                    }
                    continue;
                }

                // Reject sold-out / unavailable items
                if (!$menuItem->is_available) {
                    continue;
                }

                $price = (!empty($item['price']) && floatval($item['price']) >= floatval($menuItem->price)) ? floatval($item['price']) : floatval($menuItem->price);
                $totalAmount += $price * $quantity;

                $itemsData[] = [
                    'menu_item_id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'quantity' => $quantity,
                    'price' => $price,
                ];

                try {
                    $inventory = Inventory::where('restaurant_id', $request->restaurant_id)
                        ->where('item_name', 'LIKE', '%' . $menuItem->name . '%')
                        ->first();

                    if ($inventory) {
                        if ($inventory->quantity >= $quantity) {
                            $inventory->decrement('quantity', $quantity);
                        } else {
                            \Log::warning("Insufficient inventory for menu item: {$menuItem->name}");
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error("Error updating inventory for order: " . $e->getMessage());
                }
            }

            if (empty($itemsData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No available menu items could be ordered.'
                ], 422);
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

        $user = auth()->user();
        $query = Order::query();
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $order = $query->find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found in your restaurant'], 404);
        }

        $order->status = $request->status;
        $order->save();

        return response()->json(['success' => true, 'data' => $order]);
    }

    public function pay(Request $request, $id)
    {
        $user = auth()->user();
        $query = Order::query();
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $order = $query->find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found in your restaurant'], 404);
        }

        $order->payment_status = 'paid';
        $order->payment_method = $request->payment_method ?? 'online';
        $order->status = 'completed';
        $order->save();

        $table = \App\Models\Table::find($order->table_id);
        if ($table) {
            $table->status = 'available';
            $table->save();
        }

        return response()->json(['success' => true, 'data' => $order]);
    }

    public function getRestaurantOrders($restaurantId)
    {
        $orders = Order::where('restaurant_id', $restaurantId)->with(['items.menuItem', 'table'])->orderBy('created_at', 'desc')->paginate(50);
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

        if (!$order) {
            abort(404, 'Order not found');
        }

        $restaurant = \App\Models\Restaurant::find($order->restaurant_id) ?? new \App\Models\Restaurant([
            'name' => 'DineFlow AI Restaurant Suite',
            'address' => '456 Gourmet Boulevard, Commercial Hub, Patan, Gujarat - 384265',
            'phone' => '+91 98765 43210',
            'email' => 'billing@dineflow.ai',
            'gstin' => '24ABCDE1234F1Z5'
        ]);

        $table = \App\Models\Table::find($order->table_id);
        $tableName = $table ? $table->table_number : 'Table #' . $order->table_id;

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
            $cat = strtolower($item->menuItem?->category ?? 'food');
            return in_array($cat, ['mains', 'starters', 'breads', 'rice', 'pizza', 'burger', 'pasta', 'soup', 'main course', 'food']);
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
