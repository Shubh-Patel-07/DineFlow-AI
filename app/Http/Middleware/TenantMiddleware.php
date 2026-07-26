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
        $restaurantId = $request->route('restaurantId') ?? $request->input('restaurant_id') ?? $request->query('restaurantId');

        // Admin has access to all tenant data
        if ($user && strtolower(trim($user->role ?? '')) === 'admin') {
            return $next($request);
        }

        if ($restaurantId && $user && $user->restaurant_id && strval($user->restaurant_id) !== strval($restaurantId)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden: You do not have access to this restaurant\'s data.'
                ], 403);
            }
            abort(403, 'Tenant access denied.');
        }

        return $next($request);
    }
}
