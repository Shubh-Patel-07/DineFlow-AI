<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class TenantMiddleware
{
    /**
     * Routes that must always be accessible regardless of subscription status.
     */
    private array $bypassRoutes = [
        'subscription/status',
        'subscription/renew',
        'logout',
        'api/health',
    ];

    private array $bypassRouteNames = [
        'subscription.status',
        'auth.logout',
        'login',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user) {
            return $next($request);
        }

        // Superadmin bypasses all tenant scoping
        if (strtolower(trim($user->role ?? '')) === 'superadmin') {
            return $next($request);
        }

        // Allow whitelisted routes to pass through regardless of suspension/expiry
        foreach ($this->bypassRoutes as $route) {
            if ($request->is($route)) {
                return $next($request);
            }
        }
        foreach ($this->bypassRouteNames as $routeName) {
            if ($request->routeIs($routeName)) {
                return $next($request);
            }
        }

        // ── USER DEACTIVATION ENFORCEMENT ──────────────────────────────────────
        if (!$user->is_active) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status' => 'deactivated',
                    'message' => 'Your staff account has been deactivated. Please contact your administrator.',
                    'redirect' => '/subscription/status',
                ], 403);
            }
            return redirect()->route('subscription.status');
        }

        // ── SUBSCRIPTION STATUS ENFORCEMENT ────────────────────────────────────
        if ($user->restaurant_id) {
            $restaurant = \App\Models\Restaurant::find($user->restaurant_id);
            if ($restaurant) {
                $status = strtolower(trim($restaurant->subscription_status ?? 'active'));

                // Real-time server-authoritative expiration check
                // For 'free' plan restaurants without an explicit subscription_expires_at, they are lifetime free and should not expire via trial_ends_at
                $isLifetimeFree = strtolower(trim($restaurant->plan_type ?? 'free')) === 'free' && empty($restaurant->subscription_expires_at);

                $effectiveExpiry = null;
                if (!$isLifetimeFree) {
                    $effectiveExpiry = $restaurant->subscription_expires_at
                        ? Carbon::parse($restaurant->subscription_expires_at)
                        : ($restaurant->trial_ends_at ? Carbon::parse($restaurant->trial_ends_at) : null);
                }

                if ($effectiveExpiry) {
                    $graceDays = !is_null($restaurant->grace_period_days) ? intval($restaurant->grace_period_days) : 7;
                    $gracePeriodEnd = (clone $effectiveExpiry)->addDays($graceDays);

                    if (Carbon::now()->isAfter($gracePeriodEnd) && !in_array($status, ['suspended', 'expired', 'cancelled'])) {
                        $status = 'expired';
                        $restaurant->update(['subscription_status' => 'expired']);
                        try {
                            \App\Models\AuditLog::record('subscription_expired_auto', $restaurant->id, 'subscription', $restaurant->id, [
                                'reason' => 'Subscription and grace period exceeded'
                            ]);
                        } catch (\Throwable $e) {}
                    } elseif (Carbon::now()->isAfter($effectiveExpiry) && Carbon::now()->isBefore($gracePeriodEnd) && $status !== 'grace_period' && !in_array($status, ['suspended', 'cancelled'])) {
                        $status = 'grace_period';
                        $restaurant->update(['subscription_status' => 'grace_period']);
                    } elseif (Carbon::now()->isBefore($effectiveExpiry) && in_array($status, ['expired', 'grace_period'])) {
                        $status = ($restaurant->trial_ends_at && !$restaurant->subscription_expires_at) ? 'trial' : 'active';
                        $restaurant->update(['subscription_status' => $status, 'suspension_reason' => null, 'suspension_message' => null]);
                    }
                }

                // ── SUSPENDED: Redirect to professional status page ──
                if ($status === 'suspended') {
                    return $this->respondWithStatusRedirect($request, $restaurant, 'suspended');
                }

                // ── EXPIRED / CANCELLED: Redirect to professional status page ──
                if ($status === 'expired' || $status === 'cancelled') {
                    return $this->respondWithStatusRedirect($request, $restaurant, $status);
                }
            }
        }

        // ── CROSS-TENANT ACCESS CHECK ──────────────────────────────────────────
        $userTenantId = strval($user->restaurant_id ?? '');

        $requestedTenantId = $request->route('restaurantId')
            ?? $request->route('restaurant_id')
            ?? $request->query('restaurantId')
            ?? $request->query('restaurant_id')
            ?? $request->input('restaurant_id')
            ?? $request->input('restaurantId');

        if (($requestedTenantId === null || $requestedTenantId === '') && preg_match('#/(?:restaurants?|tables|orders|reservations|feedback|waiter-calls|expenses|staff|analytics)/restaurant/(\d+)#', $request->path(), $matches)) {
            $requestedTenantId = $matches[1];
        } elseif (($requestedTenantId === null || $requestedTenantId === '') && preg_match('#^/?api/restaurants/(\d+)(?:/settings)?#', $request->path(), $matches)) {
            $requestedTenantId = $matches[1];
        }

        if ($requestedTenantId !== null && $requestedTenantId !== '' && $userTenantId !== '' && strval($requestedTenantId) !== $userTenantId) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => "Forbidden: You do not have access to restaurant {$requestedTenantId}'s data."
                ], 403);
            }
            abort(403, 'Forbidden: Cross-tenant access denied.');
        }

        // Enforce authenticated user's restaurant_id on the request
        if ($userTenantId !== '') {
            $request->merge(['restaurant_id' => $user->restaurant_id]);
            $request->query->set('restaurantId', $user->restaurant_id);
            $request->query->set('restaurant_id', $user->restaurant_id);
        }

        return $next($request);
    }

    /**
     * Return proper response for suspended/expired restaurants.
     * API requests get structured JSON; web requests redirect to the professional status page.
     */
    private function respondWithStatusRedirect(Request $request, $restaurant, string $status): Response
    {
        $customerMsg = ($status === 'suspended')
            ? $restaurant->getCustomerFacingSuspensionMessage()
            : $restaurant->getCustomerFacingExpiryMessage();

        // For pure API requests, return structured JSON (not a raw abort)
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'status' => $status,
                'restaurant_name' => $restaurant->name,
                'message' => $customerMsg,
                'redirect' => '/subscription/status',
                'support' => [
                    'phone' => '+91 7777938338',
                    'whatsapp' => 'https://wa.me/917777938338',
                    'email' => 'shubhmpatel77@gmail.com'
                ]
            ], 403);
        }

        // For web requests, redirect to professional subscription status page
        return redirect()->route('subscription.status');
    }
}
