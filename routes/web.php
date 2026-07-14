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
        return view('dashboard');
    })->name('dashboard');
    
    Route::get('/pos-terminal', function () {
        return view('pos.terminal');
    })->name('pos.terminal');

    Route::get('/pos', function () {
        return view('pos.index');
    })->name('pos.index');
});

// Fallback Redirect
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
