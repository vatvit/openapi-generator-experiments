<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});

// API documentation endpoint
Route::get('/docs', function () {
    return response()->json([
        'message' => 'PetStore API Documentation',
        'openapi_spec' => url('/petshop-extended.yaml'),
        'version' => '1.0.0',
        'endpoints' => [
            'GET /api/health' => 'Health check',
            'GET /api/v2/pets' => 'List pets',
            'POST /api/v2/pets' => 'Create pet',
            'GET /api/v2/pets/{id}' => 'Get pet by ID',
            'DELETE /api/v2/pets/{id}' => 'Delete pet',
        ]
    ]);
});