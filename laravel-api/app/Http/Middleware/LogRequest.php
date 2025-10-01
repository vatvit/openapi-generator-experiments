<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

/**
 * Log Request Middleware
 *
 * Simple example middleware that logs incoming API requests
 * Demonstrates how to add custom middleware to specific API operations
 */
class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log request details
        Log::info('API Request', [
            'method' => $request->method(),
            'path' => $request->path(),
            'query' => $request->query(),
            'route' => $request->route()?->getName(),
        ]);

        return $next($request);
    }
}
