<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $userRole = strtolower(trim($user->role ?? ''));

        // Expand any comma-separated role definitions
        $expandedRoles = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $sub) {
                $expandedRoles[] = strtolower(trim($sub));
            }
        }

        // If the route strictly requires superadmin, only superadmin can pass
        if (in_array('superadmin', $expandedRoles)) {
            if ($userRole !== 'superadmin') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Forbidden: Superadmin privilege required."
                    ], 403);
                }
                abort(403, "Forbidden: Superadmin privilege required.");
            }
            return $next($request);
        }

        // For standard tenant routes, superadmin and admin have full access
        if ($userRole === 'superadmin' || $userRole === 'admin') {
            return $next($request);
        }

        if (!in_array($userRole, $expandedRoles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Forbidden: Role '{$userRole}' is not authorized to access this route."
                ], 403);
            }

            if ($userRole === 'cashier' || $userRole === 'waiter') {
                return redirect('/pos');
            } elseif ($userRole === 'kitchen') {
                return redirect('/kitchen');
            } elseif ($userRole === 'customer') {
                $restId = $user->restaurant_id ?: 1;
                return redirect("/customer/menu?restaurantId={$restId}&tableId=1")->with('error', 'Access restricted to customer digital menu.');
            } elseif ($userRole === 'superadmin') {
                return redirect('/superadmin');
            }

            return redirect()->route('login')->with('error', 'Please login with an authorized account.');
        }

        return $next($request);
    }
}
