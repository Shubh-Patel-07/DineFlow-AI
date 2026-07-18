<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CustomerController;
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
        // Today's Orders
        $todayOrdersCount = \App\Models\Order::whereDate('created_at', \Carbon\Carbon::today())->count();
        $yesterdayOrdersCount = \App\Models\Order::whereDate('created_at', \Carbon\Carbon::yesterday())->count();
        $ordersDiff = $todayOrdersCount - $yesterdayOrdersCount;
        $ordersDiffText = ($ordersDiff >= 0 ? '+' : '') . $ordersDiff . ' from yesterday';

        // Total Revenue
        $todayRevenue = \App\Models\Order::whereDate('created_at', \Carbon\Carbon::today())->where('payment_status', 'paid')->sum('total_amount');
        $yesterdayRevenue = \App\Models\Order::whereDate('created_at', \Carbon\Carbon::yesterday())->where('payment_status', 'paid')->sum('total_amount');
        if ($yesterdayRevenue > 0) {
            $revenueChange = round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100);
        } else {
            $revenueChange = $todayRevenue > 0 ? 100 : 0;
        }
        $revenueDiffText = ($revenueChange >= 0 ? '+' : '') . $revenueChange . '% from yesterday';

        // Occupied Tables
        $occupiedTablesCount = \App\Models\Order::where('payment_status', 'pending')->distinct('table_id')->count('table_id');
        $totalTablesCount = \App\Models\Table::count() ?: 12;

        // Active Customers
        $activeCustomers = $occupiedTablesCount * 3;

        return view('dashboard', [
            'todayOrdersCount' => $todayOrdersCount,
            'ordersDiffText' => $ordersDiffText,
            'todayRevenue' => $todayRevenue,
            'revenueDiffText' => $revenueDiffText,
            'occupiedTablesCount' => $occupiedTablesCount,
            'totalTablesCount' => $totalTablesCount,
            'activeCustomers' => $activeCustomers,
        ]);
    })->name('dashboard');
    
    Route::get('/pos-terminal', function () {
        return view('pos.terminal');
    })->name('pos.terminal');

    Route::get('/pos', function () {
        return view('pos.index');
    })->name('pos.index');
});

Route::get('/orders/{id}/receipt', [OrderController::class, 'showReceipt'])->name('orders.receipt');
Route::get('/orders/{id}/receipt/kitchen', [OrderController::class, 'showKitchenKOT'])->name('orders.receipt.kitchen');
Route::get('/orders/{id}/receipt/bar', [OrderController::class, 'showBarKOT'])->name('orders.receipt.bar');
Route::get('/orders/{id}/receipt/kot-combined', [OrderController::class, 'showCombinedKOT'])->name('orders.receipt.kot-combined');


// Simple shortcuts for testing/presentations
Route::get('/menu', function () {
    return redirect('/frontend/customer/menu.html?restaurantId=1&tableId=1');
});

Route::get('/', function () {
    return auth()->check() ? redirect()->route('pos.index') : redirect()->route('login');
});


// DineFlow AI API Endpoints (Phase 1-4)
Route::prefix('api')->group(function () {

    // --- PUBLIC ENDPOINTS ---
    Route::get('/restaurants/{id}', [RestaurantController::class, 'show']);
    Route::get('/tables/{id}', [TableController::class, 'show']);
    Route::get('/menu', [MenuItemController::class, 'index']);
    
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/pay', [OrderController::class, 'pay']);
    
    Route::post('/feedback', [CustomerController::class, 'placeFeedback']);
    Route::post('/waiter-calls', [CustomerController::class, 'callWaiter']);

    // --- PROTECTED & TENANT SECURED ENDPOINTS ---
    Route::middleware(['auth'])->group(function () {
        
        // Restaurants
        Route::post('/restaurants', [RestaurantController::class, 'store'])->middleware('role:admin');

        // Tables
        Route::post('/tables', [TableController::class, 'store'])->middleware('role:admin');
        Route::get('/tables/restaurant/{restaurantId}', [TableController::class, 'getRestaurantTables'])
            ->middleware(['role:admin,manager,cashier,waiter', 'tenant']);

        // Menu Items CRUD
        Route::post('/menu', [MenuItemController::class, 'store'])->middleware('role:admin');
        Route::put('/menu/{id}', [MenuItemController::class, 'update'])->middleware('role:admin');
        Route::delete('/menu/{id}', [MenuItemController::class, 'destroy'])->middleware('role:admin');

        // Orders Management
        Route::get('/orders/restaurant/{restaurantId}', [OrderController::class, 'getRestaurantOrders'])
            ->middleware(['role:admin,manager,cashier,kitchen,waiter', 'tenant']);
        Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])
            ->middleware('role:admin,manager,cashier,kitchen,waiter');

        // Inventory Management
        Route::get('/inventory', [InventoryController::class, 'index'])
            ->middleware(['role:admin,manager,cashier', 'tenant']);
        Route::post('/inventory', [InventoryController::class, 'store'])->middleware('role:admin');
        Route::put('/inventory/{id}', [InventoryController::class, 'update'])->middleware('role:admin');

        // Sales Analytics
        Route::get('/analytics/restaurant/{restaurantId}', [AnalyticsController::class, 'getSalesAnalytics'])
            ->middleware(['role:admin', 'tenant']);

        // Customer Reviews & Waiter Alerts
        Route::get('/feedback/restaurant/{restaurantId}', [CustomerController::class, 'getFeedbacks'])
            ->middleware(['role:admin,manager', 'tenant']);
        Route::get('/waiter-calls/restaurant/{restaurantId}', [CustomerController::class, 'getWaiterCalls'])
            ->middleware(['role:admin,manager,cashier,waiter', 'tenant']);
        Route::put('/waiter-calls/{id}/resolve', [CustomerController::class, 'resolveWaiterCall'])
            ->middleware('role:admin,manager,cashier,waiter');
    });
});
