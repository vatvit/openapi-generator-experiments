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

// TicTacToe V1 API Routes - DISABLED (V1 not regenerated yet)
// // Use GameplayController which has all operations (getBoard, getSquare, putSquare)
// Route::prefix('tictactoe')->group(function ($router) {
//     app()->bind('Tic Tac Toe', \TicTacToeApi\Server\Http\Controllers\GameplayController::class);
//     require base_path('generated/tictactoe/routes.php');
// });

// TicTacToe V2 API Routes
Route::get('/v2/test', function () {
    return response()->json(['message' => 'Test route works!']);
});

// Bind the controller name from OpenAPI spec to the concrete controller
Route::prefix('v2/tictactoe')->group(function ($router) {
    app()->bind('Tic Tac Toe', \TicTacToeApiV2\Server\Http\Controllers\DefaultController::class);
    require base_path('generated-v2/tictactoe/routes.php');
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