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
        // Register operation-specific middleware for generated routes
        // Each route has unique middleware alias with 'api.operation.' prefix to avoid collisions
        // You can either create dedicated middleware per operation or reuse same class
        $middleware->alias([
            'api.operation.findPets' => \App\Http\Middleware\OperationMiddleware::class,
            'api.operation.addPet' => \App\Http\Middleware\OperationMiddleware::class,
            'api.operation.deletePet' => \App\Http\Middleware\OperationMiddleware::class,
            'api.operation.findPetById' => \App\Http\Middleware\OperationMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
