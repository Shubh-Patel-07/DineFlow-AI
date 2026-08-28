<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\User;
use App\Models\Order;
use App\Models\Table;
use App\Models\MenuItem;
use App\Models\AuditLog;
use App\Services\PlanLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    /**
     * Render the Super Admin Dashboard Blade view
     */
    public function dashboard()
    {
        $stats = $this->calculatePlatformStats();
        return view('superadmin.index', compact('stats'));
    }

    /**
     * Paginated and searchable list of all restaurants across the SaaS platform
     */
    public function restaurants(Request $request)
    {
        $query = Restaurant::query()->with(['users' => function($q) {
            $q->where('role', 'admin')->orWhere('role', 'owner');
        }]);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('gstin', 'like', "%{$search}%");
            });
        }

        if ($request->filled('plan')) {
            $query->where('plan_type', $request->plan);
        }

        if ($request->filled('status')) {
            $query->where('subscription_status', $request->status);
        }

        $restaurants = $query->orderBy('created_at', 'desc')->paginate($request->input('per_page', 15));

        $data = $restaurants->getCollection()->map(function($restaurant) {
            $owner = $restaurant->users->first();
            $orderCount = Order::where('restaurant_id', $restaurant->id)->count();
            $revenue = Order::where('restaurant_id', $restaurant->id)
                ->where('payment_status', 'paid')
                ->sum('total_amount');

            return [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'phone' => $restaurant->phone,
                'address' => $restaurant->address,
                'plan_type' => $restaurant->plan_type ?? 'free',
                'subscription_status' => $restaurant->subscription_status ?? 'active',
                'onboarding_completed' => (bool)$restaurant->onboarding_completed,
                'plan_limits' => PlanLimitService::getLimits($restaurant->id),
                'trial_ends_at' => $restaurant->trial_ends_at ? $restaurant->trial_ends_at->toISOString() : null,
                'created_at' => $restaurant->created_at ? $restaurant->created_at->toISOString() : null,
                'owner' => $owner ? [
                    'id' => $owner->id,
                    'name' => $owner->name,
                    'email' => $owner->email,
                    'username' => $owner->username,
                ] : null,
                'orders_count' => $orderCount,
                'total_revenue' => round($revenue, 2),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $restaurants->currentPage(),
                'last_page' => $restaurants->lastPage(),
                'per_page' => $restaurants->perPage(),
                'total' => $restaurants->total(),
            ]
        ]);
    }

    /**
     * Detailed single restaurant view including full usage statistics
     */
    public function restaurantDetails($id)
    {
        $restaurant = Restaurant::with(['users'])->find($id);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $usage = PlanLimitService::getUsageStats($id);
        $totalOrders = Order::where('restaurant_id', $id)->count();
        $totalRevenue = Order::where('restaurant_id', $id)->where('payment_status', 'paid')->sum('total_amount');
        $recentOrders = Order::where('restaurant_id', $id)->orderBy('created_at', 'desc')->limit(5)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'restaurant' => $restaurant,
                'usage' => $usage,
                'total_orders' => $totalOrders,
                'total_revenue' => round($totalRevenue, 2),
                'recent_orders' => $recentOrders,
                'users' => $restaurant->users->map(fn($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'role' => $u->role,
                    'username' => $u->username,
                    'created_at' => $u->created_at
                ])
            ]
        ]);
    }

    /**
     * Super Admin creates a new Restaurant & Owner
     */
    public function createRestaurant(Request $request)
    {
        $request->validate([
            'restaurant_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'owner_password' => 'required|string|min:6',
            'phone' => 'required|string|max:30',
            'address' => 'required|string|max:500',
            'plan_type' => 'nullable|string|in:free,pro,enterprise',
        ]);

        return DB::transaction(function() use ($request) {
            $planType = $request->input('plan_type', 'free');
            $limits = PlanLimitService::getDefaultPlanLimits($planType);

            $restaurant = Restaurant::create([
                'name' => $request->restaurant_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'plan_type' => $planType,
                'subscription_status' => 'active',
                'onboarding_completed' => true,
                'plan_limits' => $limits,
                'gst_rate' => 5.00,
                'service_charge' => 5.00,
                'settings' => [
                    'currency' => 'INR',
                    'currency_symbol' => '₹',
                    'enable_kot' => true
                ]
            ]);

            $owner = User::create([
                'name' => $request->owner_name,
                'email' => $request->owner_email,
                'username' => strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->owner_name)) . rand(100, 999),
                'password' => Hash::make($request->owner_password),
                'role' => 'admin',
                'restaurant_id' => $restaurant->id,
                'numeric_pin' => (string)rand(1000, 9999),
            ]);

            AuditLog::record('restaurant_created', $restaurant->id, 'restaurant', $restaurant->id, [
                'name' => $restaurant->name,
                'owner_email' => $owner->email,
                'plan' => $planType,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Restaurant and owner account created successfully',
                'data' => [
                    'restaurant_id' => $restaurant->id,
                    'restaurant_name' => $restaurant->name,
                    'owner_email' => $owner->email,
                    'plan_type' => $restaurant->plan_type,
                ]
            ], 201);
        });
    }

    /**
     * Update restaurant details
     */
    public function updateRestaurant(Request $request, $id)
    {
        $restaurant = Restaurant::find($id);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'gstin' => 'nullable|string|max:50',
            'gst_rate' => 'nullable|numeric|min:0|max:100',
            'service_charge' => 'nullable|numeric|min:0|max:100',
        ]);

        $restaurant->update(array_filter($validated, fn($v) => !is_null($v)));

        AuditLog::record('restaurant_updated', $restaurant->id, 'restaurant', $restaurant->id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Restaurant updated successfully',
            'data' => $restaurant
        ]);
    }

    /**
     * Activate a suspended or trial restaurant
     */
    public function activateRestaurant($id)
    {
        $restaurant = Restaurant::find($id);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $restaurant->update(['subscription_status' => 'active']);

        AuditLog::record('restaurant_activated', $restaurant->id, 'restaurant', $restaurant->id, [
            'previous_status' => $restaurant->getOriginal('subscription_status')
        ]);

        return response()->json([
            'success' => true,
            'message' => "Restaurant '{$restaurant->name}' has been activated successfully."
        ]);
    }

    /**
     * Suspend a restaurant
     */
    public function suspendRestaurant($id)
    {
        $restaurant = Restaurant::find($id);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $restaurant->update(['subscription_status' => 'suspended']);

        AuditLog::record('restaurant_suspended', $restaurant->id, 'restaurant', $restaurant->id, [
            'previous_status' => $restaurant->getOriginal('subscription_status')
        ]);

        return response()->json([
            'success' => true,
            'message' => "Restaurant '{$restaurant->name}' has been suspended."
        ]);
    }

    /**
     * Change restaurant plan tier
     */
    public function updatePlan(Request $request, $id)
    {
        $request->validate([
            'plan_type' => 'required|string|in:free,pro,enterprise',
        ]);

        $restaurant = Restaurant::find($id);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $newPlan = $request->plan_type;
        $oldPlan = $restaurant->plan_type ?? 'free';
        $newLimits = PlanLimitService::getDefaultPlanLimits($newPlan);

        $restaurant->update([
            'plan_type' => $newPlan,
            'plan_limits' => $newLimits,
            'subscription_status' => 'active'
        ]);

        AuditLog::record('plan_changed', $restaurant->id, 'plan', $restaurant->id, [
            'old_plan' => $oldPlan,
            'new_plan' => $newPlan,
            'limits' => $newLimits
        ]);

        return response()->json([
            'success' => true,
            'message' => "Plan updated to " . strtoupper($newPlan) . " for '{$restaurant->name}'.",
            'data' => [
                'plan_type' => $restaurant->plan_type,
                'plan_limits' => $restaurant->plan_limits
            ]
        ]);
    }

    /**
     * Update custom quota limits for a specific restaurant
     */
    public function updatePlanLimits(Request $request, $id)
    {
        $request->validate([
            'tables' => 'required|integer|min:1|max:1000',
            'staff' => 'required|integer|min:1|max:500',
            'menu_items' => 'required|integer|min:1|max:5000',
            'monthly_orders' => 'required|integer|min:10|max:100000',
        ]);

        $restaurant = Restaurant::find($id);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $customLimits = [
            'tables' => (int)$request->tables,
            'staff' => (int)$request->staff,
            'menu_items' => (int)$request->menu_items,
            'monthly_orders' => (int)$request->monthly_orders,
        ];

        $restaurant->update(['plan_limits' => $customLimits]);

        AuditLog::record('limits_changed', $restaurant->id, 'plan', $restaurant->id, [
            'custom_limits' => $customLimits
        ]);

        return response()->json([
            'success' => true,
            'message' => "Custom quota limits updated for '{$restaurant->name}'.",
            'data' => $customLimits
        ]);
    }

    /**
     * Global Platform Stats (Cross-Tenant)
     */
    public function platformStats()
    {
        return response()->json([
            'success' => true,
            'data' => $this->calculatePlatformStats()
        ]);
    }

    /**
     * Recent Global Orders across all restaurants
     */
    public function recentOrders()
    {
        $orders = Order::with('restaurant')->orderBy('created_at', 'desc')->limit(20)->get()->map(function($order) {
            return [
                'id' => $order->id,
                'restaurant_name' => $order->restaurant?->name ?? 'Unknown',
                'table_number' => $order->table?->table_number ?? ($order->table_id ?: 'Takeaway'),
                'total_amount' => (float)$order->total_amount,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'created_at' => $order->created_at->toISOString()
            ];
        });

        return response()->json(['success' => true, 'data' => $orders]);
    }

    /**
     * Filterable Audit Logs
     */
    public function auditLogs(Request $request)
    {
        $query = AuditLog::with(['user', 'restaurant'])->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        $logs = $query->paginate($request->input('per_page', 25));

        return response()->json([
            'success' => true,
            'data' => $logs->items(),
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'total' => $logs->total(),
            ]
        ]);
    }

    /**
     * Private helper to aggregate platform metrics
     */
    private function calculatePlatformStats(): array
    {
        $totalRestaurants = Restaurant::count();
        $activeRestaurants = Restaurant::where('subscription_status', 'active')->count();
        $suspendedRestaurants = Restaurant::where('subscription_status', 'suspended')->count();
        $trialRestaurants = Restaurant::where('subscription_status', 'trial')->count();

        $freePlanCount = Restaurant::where('plan_type', 'free')->count();
        $proPlanCount = Restaurant::where('plan_type', 'pro')->count();
        $enterprisePlanCount = Restaurant::where('plan_type', 'enterprise')->count();

        $totalOrders = Order::count();
        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();
        $monthOrders = Order::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->count();

        $totalGMV = (float)Order::where('payment_status', 'paid')->sum('total_amount');
        $todayGMV = (float)Order::where('payment_status', 'paid')->whereDate('created_at', Carbon::today())->sum('total_amount');
        $monthGMV = (float)Order::where('payment_status', 'paid')->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->sum('total_amount');

        $topRestaurants = Restaurant::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'plan_type' => $r->plan_type ?? 'free',
                'orders_count' => $r->orders_count,
                'revenue' => (float)Order::where('restaurant_id', $r->id)->where('payment_status', 'paid')->sum('total_amount')
            ]);

        return [
            'total_restaurants' => $totalRestaurants,
            'active_restaurants' => $activeRestaurants,
            'suspended_restaurants' => $suspendedRestaurants,
            'trial_restaurants' => $trialRestaurants,
            'plans' => [
                'free' => $freePlanCount,
                'pro' => $proPlanCount,
                'enterprise' => $enterprisePlanCount,
            ],
            'orders' => [
                'total' => $totalOrders,
                'today' => $todayOrders,
                'this_month' => $monthOrders,
            ],
            'revenue' => [
                'total_gmv' => round($totalGMV, 2),
                'today_gmv' => round($todayGMV, 2),
                'this_month_gmv' => round($monthGMV, 2),
            ],
            'top_restaurants' => $topRestaurants,
        ];
    }
}
