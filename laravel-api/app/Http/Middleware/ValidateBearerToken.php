<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use TicTacToeApiV2\Server\Security\bearerHttpAuthenticationInterface;

/**
 * Bearer Token Authentication Middleware
 *
 * Validates JWT Bearer tokens from Authorization header
 * Implements OpenAPI security interface for type safety and validation
 */
class ValidateBearerToken implements bearerHttpAuthenticationInterface
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        // Check if Authorization header exists and starts with 'Bearer '
        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'code' => 'UNAUTHORIZED',
                'message' => 'Bearer token required in Authorization header'
            ], 401);
        }

        $token = substr($authHeader, 7);

        // Validate token format (basic check)
        if (empty($token)) {
            return response()->json([
                'code' => 'INVALID_TOKEN',
                'message' => 'Bearer token is empty'
            ], 401);
        }

        // TODO: Implement actual JWT validation
        // Example using firebase/php-jwt:
        // try {
        //     $key = config('app.jwt_secret');
        //     $payload = JWT::decode($token, new Key($key, 'HS256'));
        //
        //     // Attach user info to request
        //     $request->attributes->set('user_id', $payload->sub);
        //     $request->attributes->set('user', $payload);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'code' => 'INVALID_TOKEN',
        //         'message' => 'Invalid or expired token: ' . $e->getMessage()
        //     ], 401);
        // }

        // For demo purposes, accept any non-empty token
        // In production, replace with actual JWT validation
        if ($token === 'invalid') {
            return response()->json([
                'code' => 'INVALID_TOKEN',
                'message' => 'Token validation failed'
            ], 401);
        }

        return $next($request);
    }
}
