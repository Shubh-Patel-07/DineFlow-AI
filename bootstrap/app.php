<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->append(\App\Http\Middleware\ForceCloseConnection::class);
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'tenant' => \App\Http\Middleware\TenantMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Intercept 403 errors: if the user belongs to a suspended/expired restaurant,
        // redirect to the professional subscription status page instead of raw 403.
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $e, \Illuminate\Http\Request $request) {
            if ($response->getStatusCode() === 403 && !$request->expectsJson() && !$request->is('api/*')) {
                $user = auth()->user();
                if ($user && $user->restaurant_id && strtolower(trim($user->role ?? '')) !== 'superadmin') {
                    try {
                        $restaurant = \App\Models\Restaurant::find($user->restaurant_id);
                        if ($restaurant) {
                            $status = strtolower(trim($restaurant->subscription_status ?? 'active'));
                            if (in_array($status, ['suspended', 'expired', 'cancelled'])) {
                                return redirect('/subscription/status');
                            }
                        }
                    } catch (\Throwable $ex) {
                        // Fail gracefully — show the default 403 if DB lookup fails
                    }
                }
            }
            return $response;
        });
    })->create();

