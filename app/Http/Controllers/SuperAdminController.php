<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\User;
use App\Models\Order;
use App\Models\Table;
use App\Models\MenuItem;
use App\Models\AuditLog;
use App\Models\SubscriptionHistory;
use App\Services\PlanLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\DemoRequest;

class SuperAdminController extends Controller
{
    /**
     * Paginated and filterable list of all contact & demo leads for Super Admin
     */
    public function leads(Request $request)
    {
        $query = DemoRequest::query()->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('restaurant_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $leads = $query->paginate($request->input('per_page', 15));

        // Aggregate overview lead stats
        $totalLeads = DemoRequest::count();
        $newToday = DemoRequest::whereDate('created_at', Carbon::today())->count();
        $newOrPending = DemoRequest::whereIn('status', ['new', 'pending'])->count();
        $contacted = DemoRequest::where('status', 'contacted')->count();
        $converted = DemoRequest::where('status', 'converted')->count();
        $closed = DemoRequest::where('status', 'closed')->count();

        return response()->json([
            'success' => true,
            'data' => $leads->items(),
            'stats' => [
                'total'          => $totalLeads,
                'new_today'      => $newToday,
                'new_or_pending' => $newOrPending,
                'contacted'      => $contacted,
                'converted'      => $converted,
                'closed'         => $closed,
            ],
            'pagination' => [
                'current_page' => $leads->currentPage(),
                'last_page'    => $leads->lastPage(),
                'per_page'     => $leads->perPage(),
                'total'        => $leads->total(),
            ]
        ]);
    }

    /**
     * Update status and notes for a lead
     */
    public function updateLeadStatus(Request $request, $id)
    {
        $lead = DemoRequest::find($id);
        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'Lead not found'], 404);
        }

        $request->validate([
            'status' => 'required|string|in:new,pending,contacted,converted,closed',
            'notes'  => 'nullable|string|max:2000',
        ]);

        $oldStatus = $lead->status;
        $lead->status = $request->status;
        if ($request->has('notes')) {
            $lead->notes = $request->notes;
        }
        $lead->save();

        AuditLog::record('lead_status_updated', null, 'lead', $lead->id, [
            'restaurant_name' => $lead->restaurant_name,
            'lead_name'       => $lead->name,
            'old_status'      => $oldStatus,
            'new_status'      => $lead->status,
            'notes'           => $lead->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Lead status updated to " . ucfirst($lead->status) . ".",
            'data'    => $lead
        ]);
    }

    /**
     * Delete a spam/junk lead
     */
    public function deleteLead(Request $request, $id)
    {
        $lead = DemoRequest::find($id);
        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'Lead not found'], 404);
        }

        $leadData = [
            'name'            => $lead->name,
            'restaurant_name' => $lead->restaurant_name,
            'phone'           => $lead->phone,
        ];

        $lead->delete();

        AuditLog::record('lead_deleted', null, 'lead', $id, $leadData);

        return response()->json([
            'success' => true,
            'message' => "Lead removed successfully."
        ]);
    }

    /**
     * Render the Super Admin Dashboard Blade view
     */
    public function dashboard(Request $request = null)
    {
        $stats = $this->calculatePlatformStats();
        return view('superadmin.index', compact('stats'));
    }

    /**
     * Paginated and searchable list of all restaurants across the SaaS platform
     */
    public function restaurants(Request $request)
    {
        $query = Restaurant::where('is_demo', false)->with(['users' => function($q) {
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
                'subscription_starts_at' => $restaurant->subscription_starts_at ? $restaurant->subscription_starts_at->toISOString() : null,
                'subscription_expires_at' => $restaurant->subscription_expires_at ? $restaurant->subscription_expires_at->toISOString() : null,
                'grace_period_days' => $restaurant->grace_period_days ?: 7,
                'payment_status' => $restaurant->payment_status ?: 'paid',
                'payment_reference' => $restaurant->payment_reference,
                'billing_amount' => (float)($restaurant->billing_amount ?: 0.00),
                'suspension_reason' => $restaurant->suspension_reason,
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
     * Detailed single restaurant view including full usage statistics, subscription history, and audit log
     */
    public function restaurantDetails(Request $request, $id)
    {
        $restaurant = Restaurant::with(['users'])->find($id);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $usage = PlanLimitService::getUsageStats($id);
        $totalOrders = Order::where('restaurant_id', $id)->count();
        $totalRevenue = Order::where('restaurant_id', $id)->where('payment_status', 'paid')->sum('total_amount');
        $recentOrders = Order::where('restaurant_id', $id)->orderBy('created_at', 'desc')->limit(10)->get();
        $subscriptionHistories = SubscriptionHistory::where('restaurant_id', $id)->with('user')->orderBy('created_at', 'desc')->limit(15)->get();
        $auditLogs = AuditLog::where('restaurant_id', $id)->with('user')->orderBy('created_at', 'desc')->limit(15)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'restaurant' => $restaurant,
                'usage' => $usage,
                'total_orders' => $totalOrders,
                'total_revenue' => round($totalRevenue, 2),
                'recent_orders' => $recentOrders,
                'subscription_histories' => $subscriptionHistories,
                'audit_logs' => $auditLogs,
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
            'plan_type' => 'nullable|string|in:free,pro,enterprise,custom',
        ]);

        return DB::transaction(function() use ($request) {
            $planType = $request->input('plan_type', 'free');
            $limits = PlanLimitService::getDefaultPlanLimits($planType);

            $startsAt = Carbon::now();
            $expiresAt = ($planType === 'free') ? null : Carbon::now()->addMonth();

            $restaurant = Restaurant::create([
                'name' => $request->restaurant_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'plan_type' => $planType,
                'subscription_status' => 'active',
                'onboarding_completed' => true,
                'plan_limits' => $limits,
                'subscription_starts_at' => $startsAt,
                'subscription_expires_at' => $expiresAt,
                'grace_period_days' => 7,
                'payment_status' => 'paid',
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
                'is_active' => true,
            ]);

            SubscriptionHistory::record($restaurant, $planType, 'active', [
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'reason' => 'Initial restaurant creation by Super Admin',
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
     * Update restaurant profile details
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
     * Activate / Reactivate a restaurant
     */
    public function activateRestaurant(Request $request, $id)
    {
        $restaurant = Restaurant::find($id);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $oldStatus = $restaurant->subscription_status;
        
        $updateData = [
            'subscription_status' => 'active',
            'suspension_reason' => null,
            'suspension_message' => null,
            'suspension_internal_note' => null,
            'suspended_at' => null,
        ];
        if ($restaurant->subscription_expires_at && Carbon::parse($restaurant->subscription_expires_at)->isPast()) {
            $updateData['subscription_expires_at'] = Carbon::now()->addDays(30);
        }
        $restaurant->update($updateData);

        $reason = $request->input('reason', 'Super Admin restored/activated access');

        SubscriptionHistory::record($restaurant, $restaurant->plan_type ?? 'free', 'active', [
            'previous_status' => $oldStatus,
            'reason' => $reason,
        ]);

        AuditLog::record('restaurant_activated', $restaurant->id, 'restaurant', $restaurant->id, [
            'previous_status' => $oldStatus,
            'reason' => $reason
        ]);

        return response()->json([
            'success' => true,
            'message' => "Restaurant '{$restaurant->name}' has been activated successfully.",
            'data' => [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'subscription_status' => 'active',
            ]
        ]);
    }

    /**
     * Suspend a restaurant with professional reason, customer-facing message, and internal note
     */
    public function suspendRestaurant(Request $request, $id)
    {
        $restaurant = Restaurant::find($id);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $reason = $request->input('reason', 'Temporary Administrative Suspension');
        $customerMessage = trim((string)$request->input('customer_message', $request->input('suspension_message', '')));
        $internalNote = trim((string)$request->input('internal_note', $request->input('suspension_internal_note', '')));

        if (empty($customerMessage)) {
            $customerMessage = "Your restaurant account has been temporarily suspended by Tendorabuy Administration. Please contact support for assistance.";
        }

        $oldStatus = $restaurant->subscription_status;

        $restaurant->update([
            'subscription_status' => 'suspended',
            'suspension_reason' => $reason,
            'suspension_message' => $customerMessage,
            'suspension_internal_note' => $internalNote,
            'suspended_at' => Carbon::now(),
        ]);

        SubscriptionHistory::record($restaurant, $restaurant->plan_type ?? 'free', 'suspended', [
            'previous_status' => $oldStatus,
            'reason' => $reason,
            'customer_message' => $customerMessage,
        ]);

        AuditLog::record('restaurant_suspended', $restaurant->id, 'restaurant', $restaurant->id, [
            'previous_status' => $oldStatus,
            'reason' => $reason,
            'customer_message' => $customerMessage,
            'internal_note' => $internalNote,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Restaurant '{$restaurant->name}' has been suspended successfully.",
            'data' => [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'subscription_status' => 'suspended',
                'suspension_reason' => $reason,
                'suspension_message' => $customerMessage,
                'suspended_at' => $restaurant->suspended_at?->toDateTimeString(),
            ]
        ]);
    }

    /**
     * Change restaurant plan tier
     */
    public function updatePlan(Request $request, $id)
    {
        $request->validate([
            'plan_type' => 'required|string|in:free,pro,enterprise,custom',
            'billing_amount' => 'nullable|numeric|min:0',
        ]);

        $restaurant = Restaurant::find($id);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $newPlan = $request->plan_type;
        $oldPlan = $restaurant->plan_type ?? 'free';
        $newLimits = PlanLimitService::getDefaultPlanLimits($newPlan);

        $startsAt = Carbon::now();
        $expiresAt = ($newPlan === 'free') ? null : Carbon::now()->addMonth();

        $restaurant->update([
            'plan_type' => $newPlan,
            'plan_limits' => $newLimits,
            'subscription_status' => 'active',
            'subscription_starts_at' => $startsAt,
            'subscription_expires_at' => $expiresAt,
            'billing_amount' => $request->filled('billing_amount') ? (float)$request->billing_amount : $restaurant->billing_amount,
        ]);

        SubscriptionHistory::record($restaurant, $newPlan, 'active', [
            'previous_plan' => $oldPlan,
            'starts_at' => $startsAt,
            'expires_at' => $expiresAt,
            'billing_amount' => $restaurant->billing_amount,
            'reason' => $request->input('reason', "Plan changed to " . strtoupper($newPlan)),
        ]);

        AuditLog::record('plan_changed', $restaurant->id, 'plan', $restaurant->id, [
            'old_plan' => $oldPlan,
            'new_plan' => $newPlan,
            'limits' => $newLimits,
            'reason' => $request->input('reason')
        ]);

        return response()->json([
            'success' => true,
            'message' => "Plan updated to " . strtoupper($newPlan) . " for '{$restaurant->name}'.",
            'data' => [
                'plan_type' => $restaurant->plan_type,
                'plan_limits' => $restaurant->plan_limits,
                'subscription_expires_at' => $restaurant->subscription_expires_at ? $restaurant->subscription_expires_at->toISOString() : null
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

        $restaurant->update([
            'plan_type' => 'custom',
            'plan_limits' => $customLimits
        ]);

        SubscriptionHistory::record($restaurant, 'custom', $restaurant->subscription_status ?? 'active', [
            'reason' => 'Custom quota limits applied by Super Admin',
            'metadata' => $customLimits
        ]);

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
     * Full subscription lifecycle override (dates, trial, grace period, status)
     */
    public function updateSubscription(Request $request, $id)
    {
        $request->validate([
            'subscription_status' => 'required|string|in:trial,active,expiring,grace_period,expired,suspended,cancelled',
            'plan_type' => 'nullable|string|in:free,pro,enterprise,custom',
            'subscription_starts_at' => 'nullable|date',
            'subscription_expires_at' => 'nullable|date',
            'grace_period_days' => 'nullable|integer|min:0|max:60',
            'trial_ends_at' => 'nullable|date',
            'billing_amount' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|string|in:paid,unpaid,pending,waived',
            'reason' => 'nullable|string|max:255',
        ]);

        $restaurant = Restaurant::find($id);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $oldStatus = $restaurant->subscription_status;
        $oldPlan = $restaurant->plan_type;

        $updateData = [
            'subscription_status' => $request->subscription_status,
            'grace_period_days' => $request->filled('grace_period_days') ? (int)$request->grace_period_days : $restaurant->grace_period_days,
        ];

        if ($request->filled('plan_type')) $updateData['plan_type'] = $request->plan_type;
        if ($request->filled('subscription_starts_at')) $updateData['subscription_starts_at'] = Carbon::parse($request->subscription_starts_at);
        if ($request->filled('subscription_expires_at')) $updateData['subscription_expires_at'] = Carbon::parse($request->subscription_expires_at);
        if ($request->filled('trial_ends_at')) $updateData['trial_ends_at'] = Carbon::parse($request->trial_ends_at);
        if ($request->filled('billing_amount')) $updateData['billing_amount'] = (float)$request->billing_amount;
        if ($request->filled('payment_status')) $updateData['payment_status'] = $request->payment_status;

        $restaurant->update($updateData);

        SubscriptionHistory::record($restaurant, $restaurant->plan_type ?? 'free', $restaurant->subscription_status, [
            'previous_plan' => $oldPlan,
            'previous_status' => $oldStatus,
            'starts_at' => $restaurant->subscription_starts_at,
            'expires_at' => $restaurant->subscription_expires_at,
            'billing_amount' => $restaurant->billing_amount,
            'payment_status' => $restaurant->payment_status,
            'reason' => $request->input('reason', 'Super Admin subscription override'),
        ]);

        AuditLog::record('subscription_override', $restaurant->id, 'subscription', $restaurant->id, [
            'previous_status' => $oldStatus,
            'new_status' => $restaurant->subscription_status,
            'update_data' => $updateData,
            'reason' => $request->input('reason')
        ]);

        return response()->json([
            'success' => true,
            'message' => "Subscription updated for '{$restaurant->name}'.",
            'data' => $restaurant
        ]);
    }

    /**
     * Manually mark a subscription as paid and extend renewal
     */
    public function recordPayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:50',
            'payment_reference' => 'nullable|string|max:100',
            'extend_months' => 'nullable|integer|min:1|max:36',
            'reason' => 'nullable|string|max:255',
        ]);

        $restaurant = Restaurant::find($id);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $extendMonths = $request->input('extend_months', 1);
        $baseDate = ($restaurant->subscription_expires_at && Carbon::parse($restaurant->subscription_expires_at)->isFuture())
            ? Carbon::parse($restaurant->subscription_expires_at)
            : Carbon::now();

        $newExpiresAt = $baseDate->addMonths($extendMonths);

        $restaurant->update([
            'subscription_status' => 'active',
            'payment_status' => 'paid',
            'payment_method' => $request->payment_method,
            'payment_reference' => $request->payment_reference,
            'billing_amount' => (float)$request->amount,
            'last_payment_date' => Carbon::now(),
            'subscription_expires_at' => $newExpiresAt,
            'next_renewal_date' => $newExpiresAt,
        ]);

        SubscriptionHistory::record($restaurant, $restaurant->plan_type ?? 'free', 'active', [
            'starts_at' => $restaurant->subscription_starts_at ?: Carbon::now(),
            'expires_at' => $newExpiresAt,
            'billing_amount' => (float)$request->amount,
            'payment_status' => 'paid',
            'payment_method' => $request->payment_method,
            'payment_reference' => $request->payment_reference,
            'reason' => $request->input('reason', "Payment recorded ({$request->payment_method}). Subscription extended by {$extendMonths} month(s)."),
        ]);

        AuditLog::record('payment_recorded', $restaurant->id, 'payment', $restaurant->id, [
            'amount' => (float)$request->amount,
            'method' => $request->payment_method,
            'reference' => $request->payment_reference,
            'new_expires_at' => $newExpiresAt->toDateTimeString()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Payment recorded and subscription extended until {$newExpiresAt->toDateString()}.",
            'data' => $restaurant
        ]);
    }

    /**
     * Global Platform Stats (Cross-Tenant)
     */
    public function platformStats(Request $request = null)
    {
        return response()->json([
            'success' => true,
            'data' => $this->calculatePlatformStats()
        ]);
    }

    /**
     * Recent Global Orders across all restaurants
     */
    public function recentOrders(Request $request = null)
    {
        $orders = Order::with('restaurant')
            ->whereHas('restaurant', fn($q) => $q->where('is_demo', false))
            ->orderBy('created_at', 'desc')->limit(20)->get()->map(function($order) {
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
        $realRestaurantsQuery = Restaurant::where('is_demo', false);
        $totalRestaurants = (clone $realRestaurantsQuery)->count();
        $activeRestaurants = (clone $realRestaurantsQuery)->where('subscription_status', 'active')->count();
        $suspendedRestaurants = (clone $realRestaurantsQuery)->where('subscription_status', 'suspended')->count();
        $trialRestaurants = (clone $realRestaurantsQuery)->where('subscription_status', 'trial')->count();
        $expiringRestaurants = (clone $realRestaurantsQuery)->where('subscription_status', 'expiring')->count();
        $graceRestaurants = (clone $realRestaurantsQuery)->where('subscription_status', 'grace_period')->count();
        $expiredRestaurants = (clone $realRestaurantsQuery)->where('subscription_status', 'expired')->count();
        $activeDemoSandboxes = Restaurant::where('is_demo', true)->count();

        $freePlanCount = (clone $realRestaurantsQuery)->where('plan_type', 'free')->count();
        $proPlanCount = (clone $realRestaurantsQuery)->where('plan_type', 'pro')->count();
        $enterprisePlanCount = (clone $realRestaurantsQuery)->where('plan_type', 'enterprise')->count();
        $customPlanCount = (clone $realRestaurantsQuery)->where('plan_type', 'custom')->count();

        $realRestIds = (clone $realRestaurantsQuery)->pluck('id');

        $totalOrders = Order::whereIn('restaurant_id', $realRestIds)->count();
        $todayOrders = Order::whereIn('restaurant_id', $realRestIds)->whereDate('created_at', Carbon::today())->count();
        $monthOrders = Order::whereIn('restaurant_id', $realRestIds)->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->count();

        $totalGMV = (float)Order::whereIn('restaurant_id', $realRestIds)->where('payment_status', 'paid')->sum('total_amount');
        $todayGMV = (float)Order::whereIn('restaurant_id', $realRestIds)->where('payment_status', 'paid')->whereDate('created_at', Carbon::today())->sum('total_amount');
        $monthGMV = (float)Order::whereIn('restaurant_id', $realRestIds)->where('payment_status', 'paid')->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->sum('total_amount');

        $topRestaurants = Restaurant::where('is_demo', false)->withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'plan_type' => $r->plan_type ?? 'free',
                'subscription_status' => $r->subscription_status ?? 'active',
                'orders_count' => $r->orders_count,
                'revenue' => (float)Order::where('restaurant_id', $r->id)->where('payment_status', 'paid')->sum('total_amount')
            ]);

        return [
            'total_restaurants' => $totalRestaurants,
            'active_restaurants' => $activeRestaurants,
            'suspended_restaurants' => $suspendedRestaurants,
            'trial_restaurants' => $trialRestaurants,
            'expiring_restaurants' => $expiringRestaurants,
            'grace_restaurants' => $graceRestaurants,
            'expired_restaurants' => $expiredRestaurants,
            'plans' => [
                'free' => $freePlanCount,
                'pro' => $proPlanCount,
                'enterprise' => $enterprisePlanCount,
                'custom' => $customPlanCount,
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
