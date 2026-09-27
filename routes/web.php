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
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SubscriptionStatusController;
use Illuminate\Support\Facades\Route;

// Public SaaS Registration & Authentication Routes
Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// Traditional Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::get('/pin-login', fn() => redirect('/login'))->name('auth.pin-login');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::get('/switch-user', [AuthController::class, 'switchUser'])->name('auth.switch-user');

// Professional Subscription & Suspension Status Page
Route::get('/subscription/status', [SubscriptionStatusController::class, 'show'])->name('subscription.status')->middleware('auth');
Route::get('/subscription/renew', [SubscriptionStatusController::class, 'show'])->name('subscription.renew')->middleware('auth');

// Guided Onboarding Wizard
Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index')->middleware(['auth', 'tenant']);
Route::post('/api/onboarding/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete')->middleware(['auth', 'tenant']);
Route::get('/api/plan/usage', [OnboardingController::class, 'getUsageStats'])->name('plan.usage')->middleware(['auth', 'tenant', 'role:admin,manager,owner']);

// Helper to serve Admin HTML files with canonical source prioritization, CSRF & RBAC Role Injection
$serveAdminPage = function ($filename) {
    $user = auth()->user();
    $userRole = $user ? strtolower(trim($user->role ?? $user->username ?? 'admin')) : strtolower(trim(session('active_role') ?? 'admin'));
    if ($userRole === 'customer') {
        $restId = $user ? $user->restaurant_id : null;
        if (!$restId) {
            return redirect('/');
        }
        return redirect("/customer/menu?restaurantId={$restId}&tableId=1")->with('error', 'Access restricted to customer digital menu.');
    }

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
    $userId = $user ? $user->id : null;
    $userName = $user ? $user->name : 'User';
    $userRestaurantId = $user ? ($user->restaurant_id ?? null) : null;
    $csrfToken = csrf_token();

    // Inject CSRF token meta if empty
    $content = preg_replace('/<meta\s+name=["\']csrf-token["\']\s+content=["\'][^"\']*["\']\s*\/?>/i', '<meta name="csrf-token" content="' . $csrfToken . '">', $content);
    $content = str_replace('<input type="hidden" name="_token" value="">', '<input type="hidden" name="_token" value="' . $csrfToken . '">', $content);

    $dineflowConfig = [
        'platformName' => config('dineflow.platform_name', 'DineFlow'),
        'instagramHandle' => config('dineflow.instagram.handle', '@smp.officials'),
        'instagramUrl' => config('dineflow.instagram.url', 'https://www.instagram.com/smp.officials/'),
        'logo' => config('dineflow.logo', '/images/dineflow_logo.png'),
        'smpPill' => config('dineflow.smp.pill_text', 'SMP'),
    ];

    $script = "<script>\n  window.AUTH_USER_ID = " . json_encode($userId) . ";\n  window.AUTH_USER_NAME = " . json_encode($userName) . ";\n  window.AUTH_USER_ROLE = " . json_encode($userRole) . ";\n  window.AUTH_USER_RESTAURANT_ID = " . json_encode($userRestaurantId) . ";\n  window.CSRF_TOKEN = " . json_encode($csrfToken) . ";\n  window.DINEFLOW_CONFIG = " . json_encode($dineflowConfig) . ";\n</script>";
    $content = str_replace('<head>', "<head>\n  {$script}", $content);
    return response($content)->header('Content-Type', 'text/html; charset=UTF-8');
};

// Protected UI Pages
Route::middleware(['auth', 'tenant'])->group(function () use ($serveAdminPage) {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user && $user->role === 'superadmin') {
            return redirect('/superadmin');
        }
        $restaurantId = $user ? $user->restaurant_id : null;
        if (!$restaurantId) {
            return redirect('/onboarding');
        }

        // Today's & Yesterday's Orders (Index-friendly date ranges)
        $todayStart = \Carbon\Carbon::today()->startOfDay();
        $todayEnd = \Carbon\Carbon::today()->endOfDay();
        $yesterdayStart = \Carbon\Carbon::yesterday()->startOfDay();
        $yesterdayEnd = \Carbon\Carbon::yesterday()->endOfDay();

        $todayOrdersCount = \App\Models\Order::where('restaurant_id', $restaurantId)->whereBetween('created_at', [$todayStart, $todayEnd])->count();
        $yesterdayOrdersCount = \App\Models\Order::where('restaurant_id', $restaurantId)->whereBetween('created_at', [$yesterdayStart, $yesterdayEnd])->count();
        $ordersDiff = $todayOrdersCount - $yesterdayOrdersCount;
        $ordersDiffText = ($ordersDiff >= 0 ? '+' : '') . $ordersDiff . ' from yesterday';

        // Total Revenue (Index-friendly)
        $todayRevenue = (float)\App\Models\Order::where('restaurant_id', $restaurantId)->whereBetween('created_at', [$todayStart, $todayEnd])->where('payment_status', 'paid')->sum('total_amount');
        $yesterdayRevenue = (float)\App\Models\Order::where('restaurant_id', $restaurantId)->whereBetween('created_at', [$yesterdayStart, $yesterdayEnd])->where('payment_status', 'paid')->sum('total_amount');
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

        // Get Last 7 Days Sales Trend (Optimized to 1 single indexed query instead of 7 full table scans)
        $sevenDaysStart = \Carbon\Carbon::today()->subDays(6)->startOfDay();
        $dailySales = \App\Models\Order::where('restaurant_id', $restaurantId)
            ->where('created_at', '>=', $sevenDaysStart)
            ->where('payment_status', 'paid')
            ->selectRaw('DATE(created_at) as order_date, SUM(total_amount) as total')
            ->groupBy('order_date')
            ->pluck('total', 'order_date');

        $weeklySalesLabels = [];
        $weeklySalesData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::today()->subDays($i);
            $weeklySalesLabels[] = $date->format('D');
            $dateKey = $date->format('Y-m-d');
            $weeklySalesData[] = floatval($dailySales[$dateKey] ?? 0);
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

        // Empty category sales if none recorded (clean zero-start)
        if (empty($categorySales)) {
            $categorySales = [];
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
    })->name('dashboard')->middleware('role:admin,manager,owner');
    
    Route::get('/pos-terminal', function () {
        return redirect('/pos');
    })->name('pos.terminal');

    Route::get('/pos', function () {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        $restaurantId = $user->restaurant_id;
        if (!$restaurantId) {
            return redirect()->route('onboarding.index');
        }
        $todayOrdersCount = \App\Models\Order::where('restaurant_id', $restaurantId)->whereDate('created_at', \Carbon\Carbon::today())->count();
        $restaurant = \App\Models\Restaurant::find($restaurantId);
        $menuItems = \App\Models\MenuItem::where('restaurant_id', $restaurantId)->get();
        return view('pos.index', compact('todayOrdersCount', 'restaurantId', 'restaurant', 'menuItems'));
    })->name('pos.index')->middleware('role:admin,manager,cashier,waiter');

    Route::get('/kitchen', function () {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        $restaurantId = $user->restaurant_id;
        if (!$restaurantId) {
            return redirect()->route('onboarding.index');
        }
        $restaurant = \App\Models\Restaurant::find($restaurantId);
        return view('pos.kitchen', compact('restaurantId', 'restaurant'));
    })->name('pos.kitchen')->middleware('role:admin,manager,kitchen,chef');
    Route::get('/orders/{id}/receipt', [OrderController::class, 'showReceipt'])->name('orders.receipt')->middleware('role:admin,manager,cashier,waiter');
    Route::get('/orders/{id}/invoice-a4', [OrderController::class, 'showA4Invoice'])->name('orders.invoice.a4')->middleware('role:admin,manager,cashier,waiter');
    Route::get('/orders/{id}/receipt/kitchen', [OrderController::class, 'showKitchenKOT'])->name('orders.receipt.kitchen')->middleware('role:admin,manager,cashier,kitchen,waiter');
    Route::get('/orders/{id}/receipt/bar', [OrderController::class, 'showBarKOT'])->name('orders.receipt.bar')->middleware('role:admin,manager,cashier,kitchen,waiter');
    Route::get('/orders/{id}/receipt/kot-combined', [OrderController::class, 'showCombinedKOT'])->name('orders.receipt.kot-combined')->middleware('role:admin,manager,cashier,kitchen,waiter');

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

        $restaurant = \App\Models\Restaurant::find($restaurantId);

        return view('pos.z-report', compact('reportDate', 'grossRevenue', 'netSubtotal', 'taxTotal', 'totalOrdersCount', 'paymentSplit', 'categoryBreakdown', 'restaurant'));
    })->name('pos.z-report');

    // Admin Page Routes (With RBAC Role Injection & Role Protection)
    Route::middleware('role:admin,manager,cashier,kitchen,waiter')->group(function () use ($serveAdminPage) {
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

        Route::get('/frontend/admin/staff.html', fn() => $serveAdminPage('staff.html'))->middleware('role:admin,manager');
        Route::get('/admin/staff', fn() => $serveAdminPage('staff.html'))->middleware('role:admin,manager');

        Route::get('/frontend/admin/settings.html', fn() => $serveAdminPage('settings.html'))->middleware('role:admin,manager');
        Route::get('/admin/settings', fn() => $serveAdminPage('settings.html'))->middleware('role:admin,manager');

        Route::get('/frontend/admin/expenses.html', fn() => $serveAdminPage('expenses.html'))->middleware('role:admin,manager');
        Route::get('/admin/expenses', fn() => $serveAdminPage('expenses.html'))->middleware('role:admin,manager');

        Route::get('/frontend/admin/qr-generator.html', fn() => $serveAdminPage('qr-generator.html'))->middleware('role:admin,manager');
        Route::get('/admin/qr-generator', fn() => $serveAdminPage('qr-generator.html'))->middleware('role:admin,manager');
        Route::get('/admin/qr-poster/{tableId?}', [\App\Http\Controllers\TableController::class, 'showA4QrPoster'])->name('admin.qr-poster')->middleware('role:admin,manager');
        Route::get('/admin/qr-poster-batch', [\App\Http\Controllers\TableController::class, 'showA4QrPosterBatch'])->name('admin.qr-poster-batch')->middleware('role:admin,manager');
        Route::get('/admin/qr-print/{tableId?}', [\App\Http\Controllers\TableController::class, 'showA4QrPoster'])->middleware('role:admin,manager');
        Route::get('/admin/qr-print-all', [\App\Http\Controllers\TableController::class, 'showA4QrPosterBatch'])->middleware('role:admin,manager');

        Route::get('/frontend/admin/sales-report.html', fn() => $serveAdminPage('sales-report.html'))->middleware('role:admin,manager');
        Route::get('/admin/sales-report.html', fn() => $serveAdminPage('sales-report.html'))->middleware('role:admin,manager');
        Route::get('/admin/sales-report', fn() => $serveAdminPage('sales-report.html'))->middleware('role:admin,manager');
        Route::get('/admin/sales', fn() => $serveAdminPage('sales-report.html'))->middleware('role:admin,manager');
        Route::get('/admin/reports', fn() => $serveAdminPage('sales-report.html'))->middleware('role:admin,manager');

        Route::get('/frontend/admin/admin-dashboard.html', fn() => redirect('/dashboard'));
        Route::get('/admin/admin-dashboard', fn() => redirect('/dashboard'));
    });
});

// Protected shortcuts with auth, tenant and admin role
Route::middleware(['auth', 'tenant', 'role:admin,manager'])->group(function () use ($serveAdminPage) {
    Route::get('/sales-report', fn() => $serveAdminPage('sales-report.html'));
    Route::get('/sales-report.html', fn() => $serveAdminPage('sales-report.html'));
    Route::get('/reports', fn() => $serveAdminPage('sales-report.html'));
    Route::get('/sales', fn() => $serveAdminPage('sales-report.html'));
    Route::get('/report', fn() => $serveAdminPage('sales-report.html'));
});

Route::get('/menu', function () {
    $firstRest = \App\Models\Restaurant::where('subscription_status', 'active')->first() ?: \App\Models\Restaurant::first();
    $restId = $firstRest ? $firstRest->id : 1;
    return redirect("/frontend/customer/menu.html?restaurantId={$restId}&tableId=1&restaurant_id={$restId}&table_id=1");
});

Route::get('/customer/menu', function (Illuminate\Http\Request $request) {
    $tableParam = $request->query('tableId') ?? $request->query('table_id') ?? $request->query('table');
    $restaurantParam = $request->query('restaurantId') ?? $request->query('restaurant_id') ?? $request->query('restaurant');

    $restaurant = null;
    if ($restaurantParam) {
        $restaurant = \App\Models\Restaurant::find($restaurantParam);
    }

    $table = null;
    if ($tableParam && !$restaurant) {
        $table = \App\Models\Table::find($tableParam);
        if ($table) {
            $restaurant = \App\Models\Restaurant::find($table->restaurant_id);
        }
    }

    // Resilient fallback: if restaurant not found by param, try the first active restaurant
    if (!$restaurant) {
        $restaurant = \App\Models\Restaurant::where('subscription_status', 'active')->first() 
            ?: \App\Models\Restaurant::first();
    }

    if (!$restaurant) {
        return response('<!DOCTYPE html><html><head><title>Restaurant Not Found</title><meta name="viewport" content="width=device-width, initial-scale=1.0"><style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#0F172A;color:#F8FAFC;text-align:center;padding:20px;}h1{font-size:24px;margin-bottom:8px;color:#F87171;}p{color:#94A3B8;font-size:14px;}</style></head><body><div><h1>🍽️ Restaurant Not Found</h1><p>The requested restaurant dining menu does not exist or has been removed.</p></div></body></html>', 404)->header('Content-Type', 'text/html');
    }

    if (!$restaurant->isOperational()) {
        return response('<!DOCTYPE html><html><head><title>Restaurant Temporarily Closed</title><meta name="viewport" content="width=device-width, initial-scale=1.0"><style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#0F172A;color:#F8FAFC;text-align:center;padding:20px;}h1{font-size:24px;margin-bottom:8px;color:#FBBF24;}p{color:#94A3B8;font-size:14px;}</style></head><body><div><h1>🔒 Restaurant Temporarily Closed</h1><p>This dining venue is currently not accepting digital orders. Please check back shortly.</p></div></body></html>', 403)->header('Content-Type', 'text/html');
    }

    // Validate table strictly belongs to this restaurant
    $validTableId = '1';
    if ($tableParam) {
        $matchedTable = \App\Models\Table::where('restaurant_id', $restaurant->id)
            ->where(function($q) use ($tableParam) {
                $q->where('id', $tableParam)->orWhere('table_number', $tableParam);
            })->first();

        if ($matchedTable) {
            $validTableId = $matchedTable->id;
        } else {
            // If cross-tenant table spoofing or invalid table, fallback to restaurant's first valid table
            $firstTable = \App\Models\Table::where('restaurant_id', $restaurant->id)->first();
            $validTableId = $firstTable ? $firstTable->id : '1';
        }
    } else {
        $firstTable = \App\Models\Table::where('restaurant_id', $restaurant->id)->first();
        $validTableId = $firstTable ? $firstTable->id : '1';
    }

    return redirect("/frontend/customer/menu.html?restaurantId={$restaurant->id}&tableId={$validTableId}&restaurant_id={$restaurant->id}&table_id={$validTableId}");
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

Route::get('/demo', function () {
    return view('pages.demo');
})->name('demo');

// Isolated Sandbox Demo Mode Routes
Route::get('/demo/start', [\App\Http\Controllers\DemoSandboxController::class, 'startDemo'])->name('demo.start');
Route::match(['get', 'post'], '/demo/reset', [\App\Http\Controllers\DemoSandboxController::class, 'resetDemo'])->name('demo.reset');
Route::match(['get', 'post'], '/demo/exit', [\App\Http\Controllers\DemoSandboxController::class, 'exitDemo'])->name('demo.exit');

// Public Contact / Demo / Enquiry Form Submissions (Rate limited)
Route::post('/contact/submit', [ContactController::class, 'submit'])->middleware('throttle:15,1')->name('contact.submit');
Route::post('/api/contact/submit', [ContactController::class, 'submit'])->middleware('throttle:15,1');
Route::post('/api/demo/request', [ContactController::class, 'submit'])->middleware('throttle:15,1');


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
    Route::post('/orders', [OrderController::class, 'store'])->middleware('throttle:15,1');
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/pay', [OrderController::class, 'pay'])->middleware('auth');
    
    Route::post('/feedback', [CustomerController::class, 'placeFeedback'])->middleware('throttle:15,1');
    Route::post('/waiter-calls', [CustomerController::class, 'callWaiter'])->middleware('throttle:15,1');
    Route::post('/reservations', [ReservationController::class, 'store'])->middleware('throttle:15,1');

    Route::get('/health', function () {
        return response()->json([
            'status' => 'online',
            'timestamp' => now()->toIso8601String(),
            'version' => '2.4.0',
        ]);
    });

    Route::get('/platform/config', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'platform_name' => config('dineflow.platform_name', 'DineFlow'),
                'platform_tagline' => config('dineflow.platform_tagline', 'Restaurant Suite v2.0'),
                'logo' => config('dineflow.logo', '/images/dineflow_logo.png'),
                'logo_alt' => config('dineflow.logo_alt', '/images/dineflow_logo.jpg'),
                'instagram_handle' => config('dineflow.instagram.handle', '@smp.officials'),
                'instagram_url' => config('dineflow.instagram.url', 'https://www.instagram.com/smp.officials/'),
                'smp_pill' => config('dineflow.smp.pill_text', 'SMP'),
                'smp_asset_found' => config('dineflow.smp.asset') !== null,
            ]
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
        Route::get('/menu/template', [MenuItemController::class, 'downloadTemplate']);
        Route::post('/menu/bulk-import', [MenuItemController::class, 'bulkImport'])->middleware('role:admin,manager');
        Route::post('/menu/ai-scan', [MenuItemController::class, 'aiScanMenu'])->middleware('role:admin,manager');
        Route::put('/menu/{id}', [MenuItemController::class, 'update'])->middleware('role:admin,manager,kitchen');
        Route::delete('/menu/{id}', [MenuItemController::class, 'destroy'])->middleware('role:admin');

        // Global Orders Management (Centralized CRUD & Action API)
        Route::get('/orders/restaurant/{restaurantId}', [OrderController::class, 'getRestaurantOrders'])
            ->middleware(['role:admin,manager,cashier,kitchen,waiter', 'tenant']);
        Route::put('/orders/{id}', [OrderController::class, 'update'])
            ->middleware('role:admin,manager,cashier,waiter');
        Route::post('/orders/{id}/items', [OrderController::class, 'addItem'])
            ->middleware('role:admin,manager,cashier,waiter');
        Route::put('/orders/{id}/items/{itemId}', [OrderController::class, 'updateItem'])
            ->middleware('role:admin,manager,cashier,waiter');
        Route::delete('/orders/{id}/items/{itemId}', [OrderController::class, 'removeItem'])
            ->middleware('role:admin,manager,cashier,waiter');
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])
            ->middleware('role:admin,manager,cashier,waiter');
        Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])
            ->middleware('role:admin,manager,cashier,kitchen,waiter');
        Route::delete('/orders/{id}', [OrderController::class, 'destroy'])
            ->middleware('role:admin,owner,manager');
        Route::get('/orders/{id}/history', [OrderController::class, 'history'])
            ->middleware('role:admin,manager,cashier,waiter');

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
        Route::get('/customers/{id}', [CustomerController::class, 'show'])->middleware('role:admin,manager,cashier');
        Route::post('/customers', [CustomerController::class, 'store'])->middleware('role:admin,manager,cashier');
        Route::post('/customers/earn-points', [CustomerController::class, 'earnPoints'])->middleware('role:admin,manager,cashier');
        Route::put('/customers/{id}', [CustomerController::class, 'update'])->middleware('role:admin,manager');
        Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->middleware('role:admin');

        // Sales & AI Analytics
        Route::get('/analytics/sales-report', [AnalyticsController::class, 'getSalesReport'])
            ->middleware(['role:admin,manager,cashier', 'tenant']);
        Route::get('/analytics/restaurant/{restaurantId}/sales-report', [AnalyticsController::class, 'getSalesReport'])
            ->middleware(['role:admin,manager,cashier', 'tenant']);
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
        Route::put('/staff/{id}/reset-password', [StaffController::class, 'resetPassword'])->middleware('role:admin,manager');
        Route::put('/staff/{id}/toggle-status', [StaffController::class, 'toggleStatus'])->middleware('role:admin,manager');
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
        Route::post('/restaurants/{id}/extend', [SuperAdminController::class, 'extendSubscription']);
        Route::post('/restaurants/{id}/payment', [SuperAdminController::class, 'recordPayment']);
        Route::get('/orders', [SuperAdminController::class, 'recentOrders']);
        Route::get('/audit-logs', [SuperAdminController::class, 'auditLogs']);
        
        // Super Admin Leads & Enquiries Management
        Route::get('/leads', [SuperAdminController::class, 'leads']);
        Route::put('/leads/{id}/status', [SuperAdminController::class, 'updateLeadStatus']);
        Route::delete('/leads/{id}', [SuperAdminController::class, 'deleteLead']);
    });
});


