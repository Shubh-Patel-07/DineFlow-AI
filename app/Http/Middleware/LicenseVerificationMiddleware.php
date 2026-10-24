<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\LicenseVerificationService;

class LicenseVerificationMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Bypass internal health route
        if ($request->is('up') || $request->is('health')) {
            return $next($request);
        }

        $result = LicenseVerificationService::verify($request->getHost());

        if (!$result['valid']) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'code' => 'LICENSE_LOCKDOWN',
                    'message' => $result['reason'] ?? 'Unauthorized Host Binding: Commercial License Lock engaged.',
                    'host' => $request->getHost(),
                    'rights_owner' => config('license.owner', 'Shubh Patel'),
                    'contact' => config('license.contact_phone', '+91 77779 38338'),
                    'support_url' => config('license.support_url', 'https://wa.me/917777938338'),
                ], 403);
            }

            return response()->view('errors.license_locked', [
                'licenseResult' => $result,
            ], 403);
        }

        return $next($request);
    }
}
