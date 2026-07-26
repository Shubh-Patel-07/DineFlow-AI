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

        // Admin role gets full access to all endpoints
        $userRole = strtolower(trim($user->role ?? ''));
        if ($userRole === 'admin') {
            return $next($request);
        }

        // Expand any comma-separated role definitions
        $expandedRoles = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $sub) {
                $expandedRoles[] = strtolower(trim($sub));
            }
        }

        if (!in_array($userRole, $expandedRoles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Forbidden: Role '{$userRole}' is not authorized to access this route."
                ], 403);
            }
            abort(403, "Forbidden: Role '{$userRole}' is not authorized.");
        }

        return $next($request);
    }
}
