<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API v1 routes
Route::prefix('v1')->group(function () {

    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0'
        ]);
    });

    // User management endpoints (will match OpenAPI spec)
    Route::apiResource('users', UserController::class);

    // Additional endpoints based on OpenAPI spec
    // These will be implemented to match the openapi.yaml specification
});

// API documentation endpoint
Route::get('/docs', function () {
    return response()->json([
        'message' => 'API Documentation',
        'openapi_spec' => url('/api/openapi.yaml'),
        'version' => '1.0.0',
        'endpoints' => [
            'GET /api/v1/health' => 'Health check',
            'GET /api/v1/users' => 'List users',
            'POST /api/v1/users' => 'Create user',
            'GET /api/v1/users/{id}' => 'Get user by ID',
            'PUT /api/v1/users/{id}' => 'Update user',
            'DELETE /api/v1/users/{id}' => 'Delete user',
        ]
    ]);
});