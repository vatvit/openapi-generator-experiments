<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // === Dependency Injection Bindings ===
            // These bindings must happen early in the bootstrap process


            // === Generated API Routes ===
            // PetStore V2 API Routes (paths already include /v2 prefix)
            Route::group([], function ($router) {
                require base_path('../generated/petstore/routes.php');
            });

            // TicTacToe V2 API Routes (paths already include /v1 prefix from spec)
            Route::group([], function ($router) {
                require base_path('../generated/tictactoe/routes.php');
            });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Exclude CSRF for API routes
        $middleware->validateCsrfTokens(except: [
            '/api/*',
            '/v2/*',  // PetStore and TicTacToe routes
            '/v1/*',  // TicTacToe routes (if accessed without /v2 prefix)
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
