<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Define middleware groups for API operations that need custom middleware
        // Generated routes will automatically use these groups if they exist

        // Example: Define middleware for specific operations
        // $middleware->group('api.middlewareGroup.findPets', [
        //     \App\Http\Middleware\CacheResponse::class,
        // ]);
        //
        // $middleware->group('api.middlewareGroup.addPet', [
        //     \App\Http\Middleware\ValidateOwnership::class,
        //     \App\Http\Middleware\LogCreation::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
