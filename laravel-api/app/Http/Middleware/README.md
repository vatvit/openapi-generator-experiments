# API Operation Middleware

This directory contains custom middleware that can be applied to specific API operations.

## How It Works

Generated API routes conditionally attach middleware groups based on the operation ID. Middleware is only applied if you define the group in `bootstrap/app.php`.

## Example: LogRequest Middleware

The `LogRequest` middleware is currently active on the `findPets` operation:

```php
// bootstrap/app.php
$middleware->group('api.middlewareGroup.findPets', [
    \App\Http\Middleware\LogRequest::class,
]);
```

This means:
- ✅ `GET /api/v2/pets` - Logs every request
- ❌ `GET /api/v2/pets/{id}` - No logging (no middleware group defined)
- ❌ `POST /api/v2/pets` - No logging (no middleware group defined)
- ❌ `DELETE /api/v2/pets/{id}` - No logging (no middleware group defined)

## Testing Middleware

### Test with middleware (findPets):
```bash
curl http://localhost:8000/api/v2/pets?limit=2
docker-compose exec app tail -f storage/logs/laravel.log | grep "API Request"
```

### Test without middleware (findPetById):
```bash
curl http://localhost:8000/api/v2/pets/123
# No "API Request" log entry for this route
```

## Adding Middleware to Other Operations

To add middleware to other API operations, define the corresponding middleware group:

```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware): void {
    // Log all pet listing requests
    $middleware->group('api.middlewareGroup.findPets', [
        \App\Http\Middleware\LogRequest::class,
    ]);

    // Add authentication and validation to pet creation
    $middleware->group('api.middlewareGroup.addPet', [
        \App\Http\Middleware\ValidateOwnership::class,
        \App\Http\Middleware\LogRequest::class,
    ]);

    // Require admin role for pet deletion
    $middleware->group('api.middlewareGroup.deletePet', [
        \App\Http\Middleware\RequireAdmin::class,
        \App\Http\Middleware\LogRequest::class,
    ]);

    // Add rate limiting to single pet lookup
    $middleware->group('api.middlewareGroup.findPetById', [
        \Illuminate\Routing\Middleware\ThrottleRequests::class.':60,1',
    ]);
})
```

## Available Operations

Based on the OpenAPI specification, these middleware groups are available:

- `api.middlewareGroup.findPets` - List all pets
- `api.middlewareGroup.addPet` - Create a new pet
- `api.middlewareGroup.findPetById` - Get pet by ID
- `api.middlewareGroup.deletePet` - Delete pet by ID

## Creating Custom Middleware

Use Laravel's artisan command inside the container:

```bash
docker-compose exec app php artisan make:middleware YourMiddleware
```

Then add it to the appropriate operation group in `bootstrap/app.php`.
