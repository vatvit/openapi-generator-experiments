<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * API Key Authentication Middleware
 *
 * Validates API keys from header/query parameters
 * Generated for use with OpenAPI security schemes
 */
class ValidateApiKey
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $headerName = 'api-key'): Response
    {
        // Try to get API key from header first, then query parameter
        $apiKey = $request->header($headerName) ?? $request->query($headerName);

        if (empty($apiKey)) {
            return response()->json([
                'code' => 'UNAUTHORIZED',
                'message' => "API key required in '{$headerName}' header or query parameter"
            ], 401);
        }

        // TODO: Validate API key against your storage (database, cache, etc.)
        // Example:
        // $validKey = \App\Models\ApiKey::where('key', $apiKey)
        //     ->where('is_active', true)
        //     ->where('expires_at', '>', now())
        //     ->first();
        //
        // if (!$validKey) {
        //     return response()->json([
        //         'code' => 'INVALID_API_KEY',
        //         'message' => 'Invalid or expired API key'
        //     ], 401);
        // }
        //
        // // Attach API key info to request
        // $request->attributes->set('api_key_id', $validKey->id);
        // $request->attributes->set('api_key_owner', $validKey->user_id);

        // For demo purposes, accept specific test keys
        $validKeys = [
            'test-api-key-12345',
            'demo-key-67890',
            'valid-key-abcde'
        ];

        if (!in_array($apiKey, $validKeys)) {
            return response()->json([
                'code' => 'INVALID_API_KEY',
                'message' => 'Invalid API key'
            ], 401);
        }

        return $next($request);
    }
}
