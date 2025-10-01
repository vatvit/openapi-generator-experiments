<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Operation Middleware
 *
 * This middleware handles operation-specific logic for generated routes.
 * Each generated route has a unique middleware alias based on its operationId.
 *
 * Usage:
 * 1. Register in bootstrap/app.php:
 *    ->withMiddleware(function (Middleware $middleware) {
 *        $middleware->alias([
 *            'findPets' => \App\Http\Middleware\OperationMiddleware::class,
 *            'addPet' => \App\Http\Middleware\OperationMiddleware::class,
 *            // ... or create dedicated middleware classes per operation
 *        ]);
 *    })
 *
 * 2. The middleware detects which operation is being called via the route name
 *
 * Examples:
 * - Add default parameters to requests
 * - Apply permission checks per operation
 * - Log operation-specific events
 * - Transform request/response data
 * - Apply rate limiting per operation
 */
class OperationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Detect which operation is being called via the route name
        $route = $request->route();
        $operationId = $route ? $route->getName() : null; // e.g., 'api.findPets'

        // Extract operation from route name (remove 'api.' prefix)
        $operation = $operationId ? str_replace('api.', '', $operationId) : null;

        // Apply operation-specific logic
        if ($operation) {
            match ($operation) {
                'findPets' => $this->handleFindPets($request),
                'addPet' => $this->handleAddPet($request),
                'deletePet' => $this->handleDeletePet($request),
                'findPetById' => $this->handleFindPetById($request),
                default => null, // No specific handling for this operation
            };
        }

        return $next($request);
    }

    /**
     * Handle findPets operation
     */
    protected function handleFindPets(Request $request): void
    {
        // Example: Add default limit if not specified
        if (!$request->has('limit')) {
            $request->merge(['limit' => 10]);
        }

        // Example: Log the request
        \Log::info('FindPets operation called', [
            'user' => $request->user()?->id,
            'params' => $request->query(),
        ]);
    }

    /**
     * Handle addPet operation
     */
    protected function handleAddPet(Request $request): void
    {
        // Example: Validate permissions
        // if (!$request->user()?->can('create-pets')) {
        //     abort(403, 'Unauthorized to create pets');
        // }
    }

    /**
     * Handle deletePet operation
     */
    protected function handleDeletePet(Request $request): void
    {
        // Example: Validate permissions
        // if (!$request->user()?->can('delete-pets')) {
        //     abort(403, 'Unauthorized to delete pets');
        // }
    }

    /**
     * Handle findPetById operation
     */
    protected function handleFindPetById(Request $request): void
    {
        // Example: Log access to specific pet
        // \Log::info('Pet accessed', ['pet_id' => $request->route('id')]);
    }
}
