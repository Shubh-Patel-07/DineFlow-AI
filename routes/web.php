<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

// Traditional Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login')->middleware('guest');
Route::get('/pin-login', [AuthController::class, 'showPinLogin'])->name('auth.pin-login')->middleware('guest');
Route::post('/pin-login', [AuthController::class, 'loginWithPin'])->name('auth.login-with-pin')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth');
Route::get('/switch-user', [AuthController::class, 'switchUser'])->name('auth.switch-user')->middleware('auth');

// Protected UI Pages
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $restaurantId = ($user && strtolower(trim($user->role ?? '')) === 'admin')
            ? request()->query('restaurantId', $user->restaurant_id ?? 1)
            : ($user->restaurant_id ?? 1);

        // Today's Orders
        $todayOrdersCount = \App\Models\Order::where('restaurant_id', $restaurantId)->whereDate('created_at', \Carbon\Carbon::today())->count();
        $yesterdayOrdersCount = \App\Models\Order::where('restaurant_id', $restaurantId)->whereDate('created_at', \Carbon\Carbon::yesterday())->count();
        $ordersDiff = $todayOrdersCount - $yesterdayOrdersCount;
        $ordersDiffText = ($ordersDiff >= 0 ? '+' : '') . $ordersDiff . ' from yesterday';

        // Total Revenue
        $todayRevenue = \App\Models\Order::where('restaurant_id', $restaurantId)->whereDate('created_at', \Carbon\Carbon::today())->where('payment_status', 'paid')->sum('total_amount');
        $yesterdayRevenue = \App\Models\Order::where('restaurant_id', $restaurantId)->whereDate('created_at', \Carbon\Carbon::yesterday())->where('payment_status', 'paid')->sum('total_amount');
        if ($yesterdayRevenue > 0) {
            $revenueChange = round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100);
        } else {
            $revenueChange = $todayRevenue > 0 ? 100 : 0;
        }
        $revenueDiffText = ($revenueChange >= 0 ? '+' : '') . $revenueChange . '% from yesterday';

        // Occupied Tables
        $occupiedTablesCount = \App\Models\Order::where('restaurant_id', $restaurantId)
            ->where('payment_status', 'pending')
            ->whereNotIn('status', ['completed', 'cancelled', 'served'])
            ->distinct('table_id')
            ->count('table_id');
        $totalTablesCount = \App\Models\Table::where('restaurant_id', $restaurantId)->count() ?: 12;

        // Active Customers
        $activeCustomers = $occupiedTablesCount * 3;

        // Get Last 7 Days Sales Trend
        $weeklySalesLabels = [];
        $weeklySalesData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::today()->subDays($i);
            $weeklySalesLabels[] = $date->format('D');
            $weeklySalesData[] = \App\Models\Order::where('restaurant_id', $restaurantId)->whereDate('created_at', $date)->where('payment_status', 'paid')->sum('total_amount');
        }

        // Get Category Wise Sales Distribution (Paid orders only)
        $categorySales = \DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->where('orders.restaurant_id', $restaurantId)
            ->where('orders.payment_status', 'paid')
            ->select(\DB::raw("COALESCE(menu_items.category, 'General') as category"), \DB::raw('SUM(order_items.quantity * order_items.price) as total'))
            ->groupBy(\DB::raw("COALESCE(menu_items.category, 'General')"))
            ->pluck('total', 'category')
            ->toArray();

        // Default categories if empty to keep charts beautiful
        if (empty($categorySales)) {
            $categorySales = ['Mains' => 1200, 'Starters' => 800, 'Beverages' => 450, 'Desserts' => 300];
        }

        return view('dashboard', [
            'todayOrdersCount' => $todayOrdersCount,
            'ordersDiffText' => $ordersDiffText,
            'todayRevenue' => $todayRevenue,
            'revenueDiffText' => $revenueDiffText,
            'occupiedTablesCount' => $occupiedTablesCount,
            'totalTablesCount' => $totalTablesCount,
            'activeCustomers' => $activeCustomers,
            'weeklySalesLabels' => $weeklySalesLabels,
            'weeklySalesData' => $weeklySalesData,
            'categorySalesLabels' => array_keys($categorySales),
            'categorySalesData' => array_values($categorySales),
        ]);
    })->name('dashboard');
    
    Route::get('/pos-terminal', function () {
        return view('pos.terminal');
    })->name('pos.terminal');

    Route::get('/pos', function () {
        $user = auth()->user();
        $restaurantId = ($user && strtolower(trim($user->role ?? '')) === 'admin')
            ? request()->query('restaurantId', $user->restaurant_id ?? 1)
            : ($user->restaurant_id ?? 1);
        $todayOrdersCount = \App\Models\Order::where('restaurant_id', $restaurantId)->whereDate('created_at', \Carbon\Carbon::today())->count();
        return view('pos.index', compact('todayOrdersCount'));
    })->name('pos.index');

    Route::get('/kitchen', function () {
        return view('pos.kitchen');
    })->name('pos.kitchen');
    Route::get('/orders/{id}/receipt', [OrderController::class, 'showReceipt'])->name('orders.receipt');
    Route::get('/orders/{id}/invoice-a4', [OrderController::class, 'showA4Invoice'])->name('orders.invoice.a4');
    Route::get('/orders/{id}/receipt/kitchen', [OrderController::class, 'showKitchenKOT'])->name('orders.receipt.kitchen');
    Route::get('/orders/{id}/receipt/bar', [OrderController::class, 'showBarKOT'])->name('orders.receipt.bar');
    Route::get('/orders/{id}/receipt/kot-combined', [OrderController::class, 'showCombinedKOT'])->name('orders.receipt.kot-combined');

    Route::get('/z-report', function (Illuminate\Http\Request $request) {
        $user = auth()->user();
        $restaurantId = ($user && strtolower(trim($user->role ?? '')) === 'admin')
            ? $request->query('restaurant_id', $user->restaurant_id ?? 1)
            : ($user->restaurant_id ?? 1);

        $reportDate = $request->query('date', \Carbon\Carbon::today()->format('Y-m-d'));
        $orders = \App\Models\Order::where('restaurant_id', $restaurantId)
            ->whereDate('created_at', $reportDate)
            ->where('payment_status', 'paid')
            ->get();

        $grossRevenue = $orders->sum('total_amount');
        $netSubtotal = $grossRevenue;
        $taxTotal = $grossRevenue * 0.18; // 18% GST calculation
        $totalOrdersCount = $orders->count();

        $paymentSplit = [
            'cash' => ['count' => 0, 'amount' => 0],
            'online' => ['count' => 0, 'amount' => 0],
            'card' => ['count' => 0, 'amount' => 0],
        ];

        foreach ($orders as $order) {
            $method = strtolower($order->payment_method ?: 'cash');
            if (str_contains($method, 'upi') || str_contains($method, 'online') || str_contains($method, 'gpay')) {
                $method = 'online';
            } elseif (str_contains($method, 'card')) {
                $method = 'card';
            } else {
                $method = 'cash';
            }
            $paymentSplit[$method]['count']++;
            $paymentSplit[$method]['amount'] += $order->total_amount;
        }

        $categoryBreakdown = \DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->where('orders.restaurant_id', $restaurantId)
            ->whereDate('orders.created_at', $reportDate)
            ->where('orders.payment_status', 'paid')
            ->select(\DB::raw("COALESCE(menu_items.category, 'General') as category"), \DB::raw('SUM(order_items.quantity) as total_qty'), \DB::raw('SUM(order_items.price * order_items.quantity) as total_sales'))
            ->groupBy(\DB::raw("COALESCE(menu_items.category, 'General')"))
            ->get();

        return view('pos.z-report', compact('reportDate', 'grossRevenue', 'netSubtotal', 'taxTotal', 'totalOrdersCount', 'paymentSplit', 'categoryBreakdown'));
    })->name('pos.z-report');

    // Helper to serve Admin HTML files with dynamic Auth User Role injection and multi-path fallbacks
    $serveAdminPage = function ($filename) {
        $baseName = basename($filename);
        $candidates = [
            public_path($filename),
            public_path("frontend/admin/{$baseName}"),
            public_path("admin/{$baseName}"),
            base_path("frontend/admin/{$baseName}"),
            base_path("public/frontend/admin/{$baseName}"),
        ];

        $targetPath = null;
        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                $targetPath = $candidate;
                break;
            }
        }

        if (!$targetPath) {
            return response("File not found: {$baseName}", 404);
        }

        $content = file_get_contents($targetPath);
        $userRole = auth()->check() ? (auth()->user()->role ?? auth()->user()->username ?? 'admin') : (session('active_role') ?? 'admin');
        $script = "<script>window.AUTH_USER_ROLE = " . json_encode($userRole) . ";</script>";
        $content = str_replace('<head>', "<head>\n  {$script}", $content);
        return response($content)->header('Content-Type', 'text/html; charset=UTF-8');
    };

    // Admin Page Routes (With RBAC Role Injection)
    Route::get('/frontend/admin/menu-manager.html', fn() => $serveAdminPage('menu-manager.html'));
    Route::get('/admin/menu-manager', fn() => $serveAdminPage('menu-manager.html'));
    Route::get('/admin/menu', fn() => $serveAdminPage('menu-manager.html'));

    Route::get('/frontend/admin/inventory.html', fn() => $serveAdminPage('inventory.html'));
    Route::get('/admin/inventory', fn() => $serveAdminPage('inventory.html'));

    Route::get('/frontend/admin/coupons.html', fn() => $serveAdminPage('coupons.html'));
    Route::get('/admin/coupons', fn() => $serveAdminPage('coupons.html'));

    Route::get('/frontend/admin/customers.html', fn() => $serveAdminPage('customers.html'));
    Route::get('/admin/customers', fn() => $serveAdminPage('customers.html'));

    Route::get('/frontend/admin/tables.html', fn() => $serveAdminPage('tables.html'));
    Route::get('/admin/tables', fn() => $serveAdminPage('tables.html'));

    Route::get('/frontend/admin/feedback.html', fn() => $serveAdminPage('feedback.html'));
    Route::get('/admin/feedback', fn() => $serveAdminPage('feedback.html'));

    Route::get('/frontend/admin/staff.html', fn() => $serveAdminPage('staff.html'));
    Route::get('/admin/staff', fn() => $serveAdminPage('staff.html'));

    Route::get('/frontend/admin/settings.html', fn() => $serveAdminPage('settings.html'));
    Route::get('/admin/settings', fn() => $serveAdminPage('settings.html'));

    Route::get('/frontend/admin/expenses.html', fn() => $serveAdminPage('expenses.html'));
    Route::get('/admin/expenses', fn() => $serveAdminPage('expenses.html'));

    Route::get('/frontend/admin/qr-generator.html', fn() => $serveAdminPage('qr-generator.html'));
    Route::get('/admin/qr-generator', fn() => $serveAdminPage('qr-generator.html'));

    Route::get('/frontend/admin/sales-report.html', fn() => $serveAdminPage('sales-report.html'));
    Route::get('/admin/sales-report.html', fn() => $serveAdminPage('sales-report.html'));
    Route::get('/admin/sales-report', fn() => $serveAdminPage('sales-report.html'));
    Route::get('/admin/sales', fn() => $serveAdminPage('sales-report.html'));
    Route::get('/admin/reports', fn() => $serveAdminPage('sales-report.html'));

    Route::get('/frontend/admin/admin-dashboard.html', fn() => $serveAdminPage('admin-dashboard.html'));
    Route::get('/admin/admin-dashboard', fn() => $serveAdminPage('admin-dashboard.html'));
});

// Simple shortcuts for testing/presentations
Route::get('/sales-report', fn() => $serveAdminPage('sales-report.html'));
Route::get('/sales-report.html', fn() => $serveAdminPage('sales-report.html'));
Route::get('/reports', fn() => $serveAdminPage('sales-report.html'));
Route::get('/sales', fn() => $serveAdminPage('sales-report.html'));
Route::get('/report', fn() => $serveAdminPage('sales-report.html'));

Route::get('/menu', function () {
    return redirect('/frontend/customer/menu.html?restaurantId=1&tableId=1');
});

Route::get('/customer/menu', function (Illuminate\Http\Request $request) {
    $restaurantId = $request->query('restaurantId', '1');
    $tableId = $request->query('tableId', '1');
    return redirect("/frontend/customer/menu.html?restaurantId={$restaurantId}&tableId={$tableId}");
});

Route::get('/', function () {
    return auth()->check() ? redirect()->route('pos.index') : redirect()->route('login');
});


// DineFlow AI API Endpoints (Phase 1-4)
Route::prefix('api')->group(function () {

    // --- PUBLIC ENDPOINTS ---
    Route::get('/restaurants/{id}', [RestaurantController::class, 'show']);
    Route::get('/tables/restaurant/{restaurantId}', [TableController::class, 'getRestaurantTables']);
    Route::get('/tables/{id}', [TableController::class, 'show']);
    Route::get('/menu', [MenuItemController::class, 'index']);
    Route::get('/menu/restaurant/{restaurantId}', [MenuItemController::class, 'index']);
    
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/pay', [OrderController::class, 'pay'])->middleware('auth');
    
    Route::post('/feedback', [CustomerController::class, 'placeFeedback']);
    Route::post('/waiter-calls', [CustomerController::class, 'callWaiter']);
    Route::post('/reservations', [ReservationController::class, 'store']);

    // Coupon validation (public - used at POS checkout)
    Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);

    // --- PROTECTED & TENANT SECURED ENDPOINTS ---
    Route::middleware(['auth'])->group(function () {
        
        // Restaurants
        Route::post('/restaurants', [RestaurantController::class, 'store'])->middleware('role:admin');

        // Tables
        Route::post('/tables', [TableController::class, 'store'])->middleware('role:admin');
        Route::delete('/tables/{id}', [TableController::class, 'destroy'])->middleware('role:admin');

        // Table Reservations
        Route::get('/reservations/restaurant/{restaurantId}', [ReservationController::class, 'index'])
            ->middleware(['role:admin,manager,cashier,waiter', 'tenant']);
        Route::put('/reservations/{id}/status', [ReservationController::class, 'updateStatus'])
            ->middleware('role:admin,manager,cashier,waiter');
        Route::delete('/reservations/{id}', [ReservationController::class, 'destroy'])
            ->middleware('role:admin,manager');

        Route::post('/menu', [MenuItemController::class, 'store'])->middleware('role:admin,manager');
        Route::put('/menu/{id}', [MenuItemController::class, 'update'])->middleware('role:admin,manager,kitchen');
        Route::delete('/menu/{id}', [MenuItemController::class, 'destroy'])->middleware('role:admin');

        // Orders Management
        Route::get('/orders/restaurant/{restaurantId}', [OrderController::class, 'getRestaurantOrders'])
            ->middleware(['role:admin,manager,cashier,kitchen,waiter', 'tenant']);
        Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])
            ->middleware('role:admin,manager,cashier,kitchen,waiter');

        // Inventory Management
        Route::get('/inventory', [InventoryController::class, 'index'])
            ->middleware(['role:admin,manager,cashier', 'tenant']);
        Route::post('/inventory', [InventoryController::class, 'store'])->middleware('role:admin,manager');
        Route::put('/inventory/{id}', [InventoryController::class, 'update'])->middleware('role:admin,manager');
        Route::delete('/inventory/{id}', [InventoryController::class, 'destroy'])->middleware('role:admin');

        // Coupons Management
        Route::get('/coupons', [CouponController::class, 'index'])->middleware('role:admin,manager,cashier');
        Route::post('/coupons', [CouponController::class, 'store'])->middleware('role:admin,manager');
        Route::post('/coupons/auto-apply', [CouponController::class, 'getBestAutoApply'])->middleware('role:admin,manager,cashier');
        Route::put('/coupons/{id}', [CouponController::class, 'update'])->middleware('role:admin,manager');
        Route::delete('/coupons/{id}', [CouponController::class, 'destroy'])->middleware('role:admin');

        // Customers Management
        Route::get('/customers', [CustomerController::class, 'index'])->middleware('role:admin,manager,cashier');
        Route::post('/customers', [CustomerController::class, 'store'])->middleware('role:admin,manager,cashier');
        Route::post('/customers/earn-points', [CustomerController::class, 'earnPoints'])->middleware('role:admin,manager,cashier');
        Route::put('/customers/{id}', [CustomerController::class, 'update'])->middleware('role:admin,manager');
        Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->middleware('role:admin');

        // Sales & AI Analytics
        Route::get('/analytics/restaurant/{restaurantId}', [AnalyticsController::class, 'getSalesAnalytics'])
            ->middleware(['role:admin,manager,cashier', 'tenant']);
        Route::get('/analytics/menu-engineering/{restaurantId}', [AnalyticsController::class, 'getMenuEngineeringMatrix'])
            ->middleware(['role:admin,manager', 'tenant']);
        Route::get('/analytics/demand-forecast/{restaurantId}', [AnalyticsController::class, 'getDemandForecast'])
            ->middleware(['role:admin,manager', 'tenant']);

        // Customer Reviews & Waiter Alerts
        Route::get('/feedback/restaurant/{restaurantId}', [CustomerController::class, 'getFeedbacks'])
            ->middleware(['role:admin,manager,cashier', 'tenant']);
        Route::get('/waiter-calls/restaurant/{restaurantId}', [CustomerController::class, 'getWaiterCalls'])
            ->middleware(['role:admin,manager,cashier,waiter', 'tenant']);
        Route::put('/waiter-calls/{id}/resolve', [CustomerController::class, 'resolveWaiterCall'])
            ->middleware('role:admin,manager,cashier,waiter');
    });
});
