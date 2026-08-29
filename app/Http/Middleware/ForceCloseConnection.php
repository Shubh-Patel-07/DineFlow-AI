<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceCloseConnection
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Force the connection to close only on PHP built-in CLI server to avoid hanging mobile requests.
        // On Nginx / PHP-FPM in production, allow Nginx to manage FastCGI connection pooling natively.
        if (php_sapi_name() === 'cli-server') {
            $response->headers->set('Connection', 'close');
        }
        
        return $response;
    }
}
