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
            return $next($request);
        }

        // Admin has access to all tenant data
        if (strtolower(trim($user->role ?? '')) === 'admin') {
            return $next($request);
        }

        $restaurantId = $request->route('restaurantId') 
            ?? $request->input('restaurant_id') 
            ?? $request->query('restaurantId')
            ?? $request->query('restaurant_id');

        // If no restaurantId is provided in request, inject authenticated user's restaurant_id
        if (!$restaurantId && $user->restaurant_id) {
            $request->merge(['restaurant_id' => $user->restaurant_id]);
            $request->query->set('restaurantId', $user->restaurant_id);
            return $next($request);
        }

        if ($restaurantId && $user->restaurant_id && strval($user->restaurant_id) !== strval($restaurantId)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Forbidden: You do not have access to restaurant {$restaurantId}'s data."
                ], 403);
            }
            abort(403, 'Tenant access denied.');
        }

        return $next($request);
    }
}
