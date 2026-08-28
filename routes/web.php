<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;

// Public SaaS Registration & Authentication Routes
Route::get('/register', [RegisterController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit')->middleware('guest');

// Traditional Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login')->middleware('guest');
Route::get('/pin-login', [AuthController::class, 'showPinLogin'])->name('auth.pin-login')->middleware('guest');
Route::post('/pin-login', [AuthController::class, 'loginWithPin'])->name('auth.login-with-pin')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth');
Route::get('/switch-user', [AuthController::class, 'switchUser'])->name('auth.switch-user')->middleware('auth');

// Guided Onboarding Wizard
Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index')->middleware(['auth', 'tenant']);
Route::post('/api/onboarding/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete')->middleware(['auth', 'tenant']);
Route::get('/api/plan/usage', [OnboardingController::class, 'getUsageStats'])->name('plan.usage')->middleware(['auth', 'tenant']);

// Protected UI Pages
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $restaurantId = $user->restaurant_id;
        if (!$restaurantId) {
            abort(403, 'No restaurant assigned to this account.');
        }

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
            'usageStats' => \App\Services\PlanLimitService::getUsageStats($restaurantId),
        ]);
    })->name('dashboard');
    
    Route::get('/pos-terminal', function () {
        return view('pos.terminal');
    })->name('pos.terminal');

    Route::get('/pos', function () {
        $user = auth()->user();
        $restaurantId = $user->restaurant_id;
        if (!$restaurantId) {
            abort(403, 'No restaurant assigned to this account.');
        }
        $todayOrdersCount = \App\Models\Order::where('restaurant_id', $restaurantId)->whereDate('created_at', \Carbon\Carbon::today())->count();
        return view('pos.index', compact('todayOrdersCount', 'restaurantId'));
    })->name('pos.index');

    Route::get('/kitchen', function () {
        $user = auth()->user();
        $restaurantId = $user->restaurant_id;
        if (!$restaurantId) {
            abort(403, 'No restaurant assigned to this account.');
        }
        return view('pos.kitchen', compact('restaurantId'));
    })->name('pos.kitchen');
    Route::get('/orders/{id}/receipt', [OrderController::class, 'showReceipt'])->name('orders.receipt');
    Route::get('/orders/{id}/invoice-a4', [OrderController::class, 'showA4Invoice'])->name('orders.invoice.a4');
    Route::get('/orders/{id}/receipt/kitchen', [OrderController::class, 'showKitchenKOT'])->name('orders.receipt.kitchen');
    Route::get('/orders/{id}/receipt/bar', [OrderController::class, 'showBarKOT'])->name('orders.receipt.bar');
    Route::get('/orders/{id}/receipt/kot-combined', [OrderController::class, 'showCombinedKOT'])->name('orders.receipt.kot-combined');

    Route::get('/z-report', function (Illuminate\Http\Request $request) {
        $user = auth()->user();
        $restaurantId = $user->restaurant_id;
        if (!$restaurantId) {
            abort(403, 'No restaurant assigned to this account.');
        }

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

    // Helper to serve Admin HTML files with canonical source prioritization, CSRF & RBAC Role Injection
    $serveAdminPage = function ($filename) {
        $baseName = basename($filename);
        $candidates = [
            base_path("frontend/admin/{$baseName}"),
            public_path("frontend/admin/{$baseName}"),
            public_path("admin/{$baseName}"),
            public_path($filename),
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
        $user = auth()->user();
        $userId = $user ? $user->id : null;
        $userName = $user ? $user->name : 'User';
        $userRole = $user ? ($user->role ?? $user->username ?? 'admin') : (session('active_role') ?? 'admin');
        $userRestaurantId = $user ? ($user->restaurant_id ?? null) : null;
        $csrfToken = csrf_token();

        // Inject CSRF token meta if empty
        $content = preg_replace('/<meta\s+name=["\']csrf-token["\']\s+content=["\'][^"\']*["\']\s*\/?>/i', '<meta name="csrf-token" content="' . $csrfToken . '">', $content);
        $content = str_replace('<input type="hidden" name="_token" value="">', '<input type="hidden" name="_token" value="' . $csrfToken . '">', $content);

        $script = "<script>\n  window.AUTH_USER_ID = " . json_encode($userId) . ";\n  window.AUTH_USER_NAME = " . json_encode($userName) . ";\n  window.AUTH_USER_ROLE = " . json_encode($userRole) . ";\n  window.AUTH_USER_RESTAURANT_ID = " . json_encode($userRestaurantId) . ";\n  window.CSRF_TOKEN = " . json_encode($csrfToken) . ";\n</script>";
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

    Route::get('/frontend/admin/admin-dashboard.html', fn() => redirect('/dashboard'));
    Route::get('/admin/admin-dashboard', fn() => redirect('/dashboard'));
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

// Public SaaS Landing, Pricing & Legal Pages
Route::get('/', function () {
    return view('landing');
})->name('home');

Route::get('/pricing', function () {
    return view('pricing');
})->name('pricing');

Route::get('/terms', function () {
    return view('pages.terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('privacy');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');


// DineFlow AI API Endpoints (Phase 1-4)
Route::prefix('api')->group(function () {

    // --- PUBLIC ENDPOINTS ---
    Route::get('/restaurants/{id}', [RestaurantController::class, 'show']);
    Route::get('/restaurants/{id}/settings', [RestaurantController::class, 'getSettings']);
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

    Route::get('/health', function () {
        return response()->json([
            'status' => 'online',
            'timestamp' => now()->toIso8601String(),
            'version' => '2.4.0',
        ]);
    });

    // Coupon validation (public - used at POS checkout)
    Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);

    // --- PROTECTED & TENANT SECURED ENDPOINTS ---
    Route::middleware(['auth', 'tenant'])->group(function () {
        
        // Offline Sync Gateway Endpoints
        Route::post('/sync/orders', function (Illuminate\Http\Request $request, \App\Services\OfflineSyncQueueService $syncService) {
            $user = auth()->user();
            $restaurantId = $user ? $user->restaurant_id : null;
            if (!$restaurantId) {
                return response()->json(['success' => false, 'message' => 'No authenticated restaurant tenant found.'], 403);
            }
            $result = $syncService->syncIncomingOrder($request->all(), $restaurantId);
            $statusCode = $result['success'] ? 200 : 422;
            return response()->json($result, $statusCode);
        })->middleware('role:admin,manager,cashier');

        Route::get('/sync/orders/since', function (Illuminate\Http\Request $request, \App\Services\OfflineSyncQueueService $syncService) {
            $user = auth()->user();
            $restaurantId = $user ? $user->restaurant_id : null;
            if (!$restaurantId) {
                return response()->json(['success' => false, 'message' => 'No authenticated restaurant tenant found.'], 403);
            }
            $since = $request->query('since');
            $limit = min(100, intval($request->query('limit', 50)));
            $result = $syncService->getOrdersSince($restaurantId, $since, $limit);
            return response()->json($result, 200);
        })->middleware('role:admin,manager,cashier');

        // Restaurants
        Route::post('/restaurants', [RestaurantController::class, 'store'])->middleware('role:admin');
        Route::post('/restaurants/{id}/settings', [RestaurantController::class, 'updateSettings'])->middleware('role:admin,manager');

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

        // Expenses Management
        Route::get('/expenses/restaurant/{restaurantId}', [ExpenseController::class, 'index'])
            ->middleware(['role:admin,manager,cashier', 'tenant']);
        Route::get('/expenses', [ExpenseController::class, 'index'])->middleware('role:admin,manager,cashier');
        Route::post('/expenses', [ExpenseController::class, 'store'])->middleware('role:admin,manager');
        Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->middleware('role:admin,manager');
        Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->middleware('role:admin,manager');

        // Staff & Roles Management
        Route::get('/staff/restaurant/{restaurantId}', [StaffController::class, 'index'])
            ->middleware(['role:admin,manager', 'tenant']);
        Route::get('/staff', [StaffController::class, 'index'])->middleware('role:admin,manager');
        Route::post('/staff', [StaffController::class, 'store'])->middleware('role:admin,manager');
        Route::put('/staff/{id}', [StaffController::class, 'update'])->middleware('role:admin,manager');
        Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->middleware('role:admin');

        // File & Image Storage
        Route::post('/upload/image', [RestaurantController::class, 'uploadImage'])->middleware('role:admin,manager');
    });
});

// ── SUPER ADMIN PLATFORM ROUTES (Role: superadmin ONLY) ────────────────
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/superadmin', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/superadmin/restaurants', [SuperAdminController::class, 'dashboard'])->name('superadmin.restaurants');
    Route::get('/superadmin/plans', [SuperAdminController::class, 'dashboard'])->name('superadmin.plans');
    Route::get('/superadmin/analytics', [SuperAdminController::class, 'dashboard'])->name('superadmin.analytics');
    Route::get('/superadmin/audit-logs', [SuperAdminController::class, 'dashboard'])->name('superadmin.audit-logs');

    // Super Admin REST APIs
    Route::prefix('api/superadmin')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'platformStats']);
        Route::get('/stats', [SuperAdminController::class, 'platformStats']);
        Route::get('/restaurants', [SuperAdminController::class, 'restaurants']);
        Route::get('/restaurants/{id}', [SuperAdminController::class, 'restaurantDetails']);
        Route::post('/restaurants', [SuperAdminController::class, 'createRestaurant']);
        Route::put('/restaurants/{id}', [SuperAdminController::class, 'updateRestaurant']);
        Route::post('/restaurants/{id}/activate', [SuperAdminController::class, 'activateRestaurant']);
        Route::post('/restaurants/{id}/suspend', [SuperAdminController::class, 'suspendRestaurant']);
        Route::post('/restaurants/{id}/plan', [SuperAdminController::class, 'updatePlan']);
        Route::post('/restaurants/{id}/limits', [SuperAdminController::class, 'updatePlanLimits']);
        Route::post('/restaurants/{id}/subscription', [SuperAdminController::class, 'updateSubscription']);
        Route::post('/restaurants/{id}/payment', [SuperAdminController::class, 'recordPayment']);
        Route::get('/orders', [SuperAdminController::class, 'recentOrders']);
        Route::get('/audit-logs', [SuperAdminController::class, 'auditLogs']);
    });
});


