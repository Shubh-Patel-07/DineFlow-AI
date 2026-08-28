<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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

        // Check if tenant is suspended
        if ($user->restaurant_id) {
            $restaurant = \App\Models\Restaurant::find($user->restaurant_id);
            if ($restaurant && $restaurant->subscription_status === 'suspended') {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your restaurant account has been suspended. Please contact support.'
                    ], 403);
                }
                abort(403, 'Your restaurant account has been suspended. Please contact support.');
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
