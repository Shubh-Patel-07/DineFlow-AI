<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Table;
use App\Models\Restaurant;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access to order logs.'], 401);
        }
        $restaurantId = $user->restaurant_id;
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
            'items' => 'required|array|min:1',
        ]);

        try {
            $order = $this->orderService->createOrder($request->all(), auth()->user());
            return response()->json(['success' => true, 'data' => $order], 201);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['success' => false, 'message' => $ve->getMessage(), 'errors' => $ve->errors()], 422);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $he) {
            return response()->json(['success' => false, 'message' => $he->getMessage()], $he->getStatusCode());
        } catch (\Exception $e) {
            Log::error('Order creation error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id = null, $extraId = null)
    {
        if ($id instanceof Request || is_object($id)) {
            $id = $extraId ?? ($id instanceof Request ? ($id->route('id') ?? $id->input('id')) : null);
        }

        $user = auth()->user();
        $query = Order::with(['items.menuItem', 'table']);
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $order = $query->find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found in your restaurant'], 404);
        }

        $billBreakdown = $this->orderService->calculateBillBreakdown($order);

        return response()->json([
            'success' => true,
            'data' => $order,
            'bill_breakdown' => $billBreakdown,
        ])
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache');
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        try {
            $updated = $this->orderService->updateOrder($order, $request->all(), auth()->user());
            return response()->json([
                'success' => true,
                'data' => $updated,
                'bill_breakdown' => $this->orderService->calculateBillBreakdown($updated),
                'message' => 'Order updated successfully'
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $he) {
            return response()->json(['success' => false, 'message' => $he->getMessage()], $he->getStatusCode());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function addItem(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        try {
            $item = $this->orderService->addItem($order, $request->all(), auth()->user());
            $freshOrder = $order->fresh(['items.menuItem', 'table']);
            return response()->json([
                'success' => true,
                'data' => $item,
                'order' => $freshOrder,
                'bill_breakdown' => $this->orderService->calculateBillBreakdown($freshOrder),
                'message' => 'Item added successfully'
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $he) {
            return response()->json(['success' => false, 'message' => $he->getMessage()], $he->getStatusCode());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateItem(Request $request, $id, $itemId)
    {
        $order = Order::findOrFail($id);
        try {
            $item = $this->orderService->updateItem($order, $itemId, $request->all(), auth()->user());
            $freshOrder = $order->fresh(['items.menuItem', 'table']);
            return response()->json([
                'success' => true,
                'data' => $item,
                'order' => $freshOrder,
                'bill_breakdown' => $this->orderService->calculateBillBreakdown($freshOrder),
                'message' => 'Item updated successfully'
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $he) {
            return response()->json(['success' => false, 'message' => $he->getMessage()], $he->getStatusCode());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function removeItem($id, $itemId)
    {
        $order = Order::findOrFail($id);
        try {
            $this->orderService->removeItem($order, $itemId, auth()->user());
            $freshOrder = $order->fresh(['items.menuItem', 'table']);
            return response()->json([
                'success' => true,
                'order' => $freshOrder,
                'bill_breakdown' => $this->orderService->calculateBillBreakdown($freshOrder),
                'message' => 'Item removed from order'
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $he) {
            return response()->json(['success' => false, 'message' => $he->getMessage()], $he->getStatusCode());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function cancel(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $reason = $request->input('reason', 'Customer Cancelled');
        try {
            $cancelled = $this->orderService->cancelOrder($order, $reason, auth()->user());
            return response()->json([
                'success' => true,
                'data' => $cancelled,
                'message' => 'Order #' . $order->id . ' cancelled successfully'
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $he) {
            return response()->json(['success' => false, 'message' => $he->getMessage()], $he->getStatusCode());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,cooking,ready,served,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);
        try {
            $updated = $this->orderService->updateStatus($order, $request->status, auth()->user());
            return response()->json(['success' => true, 'data' => $updated]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $he) {
            return response()->json(['success' => false, 'message' => $he->getMessage()], $he->getStatusCode());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function pay(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $method = $request->payment_method ?? 'cash';
        try {
            $paid = $this->orderService->processPayment($order, $method, auth()->user(), $request->input('transaction_ref'));
            return response()->json([
                'success' => true,
                'data' => $paid,
                'bill_breakdown' => $this->orderService->calculateBillBreakdown($paid),
                'message' => 'Payment recorded successfully'
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $he) {
            return response()->json(['success' => false, 'message' => $he->getMessage()], $he->getStatusCode());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        try {
            $this->orderService->deleteOrder($order, auth()->user());
            return response()->json(['success' => true, 'message' => 'Order deleted successfully']);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $he) {
            return response()->json(['success' => false, 'message' => $he->getMessage()], $he->getStatusCode());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function history($id)
    {
        $order = Order::findOrFail($id);
        $this->orderService->assertTenantAccess($order, auth()->user());
        $history = $this->orderService->getOrderHistory($order);
        return response()->json(['success' => true, 'data' => $history]);
    }

    public function getRestaurantOrders($restaurantId)
    {
        $user = auth()->user();
        if ($user && $user->role !== 'superadmin' && (int)$user->restaurant_id !== (int)$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized tenant orders access.'], 403);
        }
        $orders = Order::where('restaurant_id', $restaurantId)
            ->with(['items.menuItem', 'table'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);
        return response()->json(['success' => true, 'data' => $orders]);
    }

    public function showReceipt($id)
    {
        $user = auth()->user();
        $query = Order::with('items');
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $order = $query->find($id);

        if (!$order) {
            abort(404, 'Order not found');
        }

        $restaurant = Restaurant::find($order->restaurant_id);
        if (!$restaurant) {
            $restaurant = new Restaurant([
                'name' => 'Restaurant #' . $order->restaurant_id,
                'address' => 'Local Address',
                'phone' => '+91 99999 00000',
                'email' => 'contact@restaurant.com'
            ]);
        }

        $table = Table::find($order->table_id);
        $tableName = $table ? $table->table_number : 'Table #' . $order->table_id;
        $isDemo = (bool)($restaurant->is_demo);

        return view('pos.receipt', compact('order', 'restaurant', 'tableName', 'isDemo'));
    }

    public function showA4Invoice(Request $request, $id)
    {
        $user = auth()->user();
        $order = null;
        if (is_numeric($id) && $id > 0) {
            $query = Order::with('items.menuItem');
            if ($user && $user->restaurant_id) {
                $query->where('restaurant_id', $user->restaurant_id);
            }
            $order = $query->find($id);
        } elseif ($id === 'live' && $user && $user->restaurant_id) {
            $tableParam = $request->query('table', 'Walk-In');
            $itemsJson = $request->query('items', '[]');
            $itemsArr = json_decode($itemsJson, true) ?: [];

            $order = new Order([
                'id' => 0,
                'restaurant_id' => $user->restaurant_id,
                'total_amount' => 0,
                'status' => 'active',
                'payment_status' => 'pending',
                'payment_method' => 'cash',
                'created_at' => now(),
            ]);
            $subtotal = 0;
            $orderItems = collect();
            foreach ($itemsArr as $rawItem) {
                $qty = (int)($rawItem['qty'] ?? 1);
                $price = (float)($rawItem['price'] ?? 0);
                $subtotal += $price * $qty;
                $orderItems->push(new OrderItem([
                    'name' => $rawItem['name'] ?? 'Item',
                    'quantity' => $qty,
                    'price' => $price,
                ]));
            }
            $order->total_amount = $subtotal;
            $order->setRelation('items', $orderItems);
            $tableName = $tableParam;
        }

        if (!$order) {
            abort(404, 'Order not found');
        }

        $restaurant = Restaurant::find($order->restaurant_id);
        if (!$restaurant) {
            $restaurant = new Restaurant([
                'name' => 'Restaurant #' . $order->restaurant_id,
                'address' => 'Local Address',
                'phone' => '+91 99999 00000',
                'email' => 'contact@restaurant.com',
                'gstin' => ''
            ]);
        }

        if (!isset($tableName)) {
            $table = Table::find($order->table_id);
            $tableName = $table ? $table->table_number : 'Table #' . $order->table_id;
        }
        $isDemo = (bool)($restaurant->is_demo);

        return view('pos.invoice_a4', compact('order', 'restaurant', 'tableName', 'isDemo'));
    }

    public function showKitchenKOT($id)
    {
        $user = auth()->user();
        $query = Order::with('items.menuItem');
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $order = $query->find($id);
        if (!$order) {
            abort(404, 'Order not found');
        }

        $restaurant = Restaurant::find($order->restaurant_id);
        if (!$restaurant) {
            $restaurant = new Restaurant([
                'name' => 'Restaurant #' . $order->restaurant_id,
                'address' => 'Local Address',
                'phone' => '+91 99999 00000',
                'email' => 'contact@restaurant.com'
            ]);
        }

        $table = Table::find($order->table_id);
        $tableName = $table ? $table->table_number : 'Table #' . $order->table_id;

        $barCategories = [
            'beverages', 'beverage', 'drinks', 'drink', 'cold drinks', 'cold drink',
            'soft drinks', 'soft drink', 'milk shek', 'milk shake', 'milk shakes',
            'milkshake', 'milkshakes', 'bar', 'cocktail', 'cocktails', 'mocktail',
            'mocktails', 'juices', 'juice', 'shakes', 'shake', 'ice cream', 'ice creams',
            'icecream', 'dessert', 'desserts'
        ];

        $kitchenItems = $order->items->filter(function ($item) use ($barCategories) {
            $cat = strtolower(trim($item->menuItem?->category ?? ''));
            return !in_array($cat, $barCategories);
        });

        $lang = \App\Services\KotLanguageService::resolveKotLanguage($order);
        $kotDict = \App\Services\KotLanguageService::getKotDictionary($lang);

        return view('pos.kot_kitchen', compact('order', 'restaurant', 'kitchenItems', 'tableName', 'lang', 'kotDict'));
    }

    public function showBarKOT($id)
    {
        $user = auth()->user();
        $query = Order::with('items.menuItem');
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $order = $query->find($id);
        if (!$order) {
            abort(404, 'Order not found');
        }

        $restaurant = Restaurant::find($order->restaurant_id);
        if (!$restaurant) {
            $restaurant = new Restaurant([
                'name' => 'Restaurant #' . $order->restaurant_id,
                'address' => 'Local Address',
                'phone' => '+91 99999 00000',
                'email' => 'contact@restaurant.com'
            ]);
        }

        $table = Table::find($order->table_id);
        $tableName = $table ? $table->table_number : 'Table #' . $order->table_id;

        $barCategories = [
            'beverages', 'beverage', 'drinks', 'drink', 'cold drinks', 'cold drink',
            'soft drinks', 'soft drink', 'milk shek', 'milk shake', 'milk shakes',
            'milkshake', 'milkshakes', 'bar', 'cocktail', 'cocktails', 'mocktail',
            'mocktails', 'juices', 'juice', 'shakes', 'shake', 'ice cream', 'ice creams',
            'icecream', 'dessert', 'desserts'
        ];

        $barItems = $order->items->filter(function ($item) use ($barCategories) {
            $cat = strtolower(trim($item->menuItem?->category ?? ''));
            return in_array($cat, $barCategories);
        });

        $lang = \App\Services\KotLanguageService::resolveKotLanguage($order);
        $kotDict = \App\Services\KotLanguageService::getKotDictionary($lang);

        return view('pos.kot_bar', compact('order', 'restaurant', 'barItems', 'tableName', 'lang', 'kotDict'));
    }

    public function showCombinedKOT($id)
    {
        $user = auth()->user();
        $query = Order::with('items.menuItem');
        if ($user && $user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        $order = $query->find($id);
        if (!$order) {
            abort(404, 'Order not found');
        }

        $restaurant = Restaurant::find($order->restaurant_id);
        if (!$restaurant) {
            $restaurant = new Restaurant([
                'name' => 'Restaurant #' . $order->restaurant_id,
                'address' => 'Local Address',
                'phone' => '+91 99999 00000',
                'email' => 'contact@restaurant.com'
            ]);
        }

        $table = Table::find($order->table_id);
        $tableName = $table ? $table->table_number : 'Table #' . $order->table_id;

        $lang = \App\Services\KotLanguageService::resolveKotLanguage($order);
        $kotDict = \App\Services\KotLanguageService::getKotDictionary($lang);

        return view('pos.kot_combined', compact('order', 'restaurant', 'tableName', 'lang', 'kotDict'));
    }
}
