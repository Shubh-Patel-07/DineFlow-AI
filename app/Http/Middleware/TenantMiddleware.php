<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user) {
            // Unauthenticated public request (e.g. customer QR endpoints)
            return $next($request);
        }

        // Only a global superadmin can bypass tenant scoping
        if (strtolower(trim($user->role ?? '')) === 'superadmin') {
            return $next($request);
        }

        // Allow status page and logout routes to pass through regardless of suspension
        if ($request->is('subscription/status') || $request->routeIs('subscription.status') || $request->is('logout') || $request->routeIs('auth.logout')) {
            return $next($request);
        }

        // Check if tenant is operational (active, trial, grace_period vs suspended, expired, cancelled)
        if ($user->restaurant_id) {
            $restaurant = \App\Models\Restaurant::find($user->restaurant_id);
            if ($restaurant) {
                $status = strtolower(trim($restaurant->subscription_status ?? 'active'));
                
                // Real-time server authoritative expiration check
                $effectiveExpiry = $restaurant->subscription_expires_at 
                    ? Carbon::parse($restaurant->subscription_expires_at) 
                    : ($restaurant->trial_ends_at ? Carbon::parse($restaurant->trial_ends_at) : null);

                if ($effectiveExpiry) {
                    $graceDays = !is_null($restaurant->grace_period_days) ? intval($restaurant->grace_period_days) : 7;
                    $gracePeriodEnd = (clone $effectiveExpiry)->addDays($graceDays);
                    
                    if (Carbon::now()->isAfter($gracePeriodEnd) && !in_array($status, ['suspended', 'expired', 'cancelled'])) {
                        $status = 'expired';
                        $restaurant->update(['subscription_status' => 'expired']);
                        \App\Models\AuditLog::record('subscription_expired_auto', $restaurant->id, 'subscription', $restaurant->id, [
                            'reason' => 'Subscription and grace period exceeded'
                        ]);
                    } elseif (Carbon::now()->isAfter($effectiveExpiry) && Carbon::now()->isBefore($gracePeriodEnd) && $status !== 'grace_period' && !in_array($status, ['suspended', 'cancelled'])) {
                        $status = 'grace_period';
                        $restaurant->update(['subscription_status' => 'grace_period']);
                    } elseif (Carbon::now()->isBefore($effectiveExpiry) && in_array($status, ['expired', 'grace_period'])) {
                        $status = ($restaurant->trial_ends_at && !$restaurant->subscription_expires_at) ? 'trial' : 'active';
                        $restaurant->update(['subscription_status' => $status, 'suspension_reason' => null, 'suspension_message' => null]);
                    }
                }

                if ($status === 'suspended') {
                    $customerMsg = $restaurant->getCustomerFacingSuspensionMessage();
                    if ($request->is('api/*') || ($request->expectsJson() && !$request->is('dashboard') && !$request->is('pos') && !$request->is('kitchen') && !$request->is('admin/*') && !$request->is('frontend/*'))) {
                        return response()->json([
                            'success' => false,
                            'status' => 'suspended',
                            'restaurant_name' => $restaurant->name,
                            'message' => $customerMsg,
                            'reason' => $restaurant->suspension_reason,
                            'redirect' => '/subscription/status',
                            'support' => [
                                'phone' => '+91 7777938338',
                                'whatsapp' => 'https://wa.me/917777938338',
                                'email' => 'support@tendorabuy.in'
                            ]
                        ], 403);
                    }
                    return redirect()->route('subscription.status');
                }

                if ($status === 'expired' || $status === 'cancelled') {
                    $customerMsg = $restaurant->getCustomerFacingExpiryMessage();
                    if ($request->is('api/*') || ($request->expectsJson() && !$request->is('dashboard') && !$request->is('pos') && !$request->is('kitchen') && !$request->is('admin/*') && !$request->is('frontend/*'))) {
                        return response()->json([
                            'success' => false,
                            'status' => 'expired',
                            'restaurant_name' => $restaurant->name,
                            'message' => $customerMsg,
                            'redirect' => '/subscription/status',
                            'support' => [
                                'phone' => '+91 7777938338',
                                'whatsapp' => 'https://wa.me/917777938338',
                                'email' => 'support@tendorabuy.in'
                            ]
                        ], 403);
                    }
                    return redirect()->route('subscription.status');
                }
            }
        }

        $userTenantId = strval($user->restaurant_id ?? '');

        // Extract requested tenant ID from route, query, input, or URI path
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

        // If a specific tenant was requested and doesn't match the user's restaurant, DENY access
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
}
