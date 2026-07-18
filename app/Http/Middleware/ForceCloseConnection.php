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
        
        // Force the connection to close immediately. This prevents the single-threaded/multi-threaded
        // PHP built-in CLI server from hanging on HTTP keep-alive connections from mobile browsers.
        $response->headers->set('Connection', 'close');
        
        return $response;
    }
}
