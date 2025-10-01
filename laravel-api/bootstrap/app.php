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
        // Each route has unique middleware alias based on operationId
        // You can either create dedicated middleware per operation or reuse same class
        $middleware->alias([
            'findPets' => \App\Http\Middleware\OperationMiddleware::class,
            'addPet' => \App\Http\Middleware\OperationMiddleware::class,
            'deletePet' => \App\Http\Middleware\OperationMiddleware::class,
            'findPetById' => \App\Http\Middleware\OperationMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
