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
            // Bind controller name from OpenAPI spec to concrete implementation
            app()->bind('PetStoreApiController', \App\Http\Controllers\Api\PetStoreController::class);

            // Register generated API routes from OpenAPI scaffolding
            // Routes are loaded within a closure to pass $router variable
            Route::group([], function ($router) {
                require base_path('generated/scaffolding/routes.php');
            });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Define middleware groups for API operations that need custom middleware
        // Generated routes will automatically use these groups if they exist

        // Example: Add logging to findPets operation
        $middleware->group('api.middlewareGroup.findPets', [
            \App\Http\Middleware\LogRequest::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
