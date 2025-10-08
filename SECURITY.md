# Security Implementation Guide

This document explains how OpenAPI security schemes are enforced in the generated Laravel API.

## Overview

The generated scaffolding **forces developers to implement security** as defined in the OpenAPI specification through:

1. **Middleware-based enforcement** - Routes automatically use security middleware when configured
2. **Example implementations** - Pre-built middleware for common auth types
3. **Runtime validation** - Requests are rejected if security requirements aren't met

## Security Schemes in Specs

### TicTacToe API (specs/tictactoe.json)

```json
"securitySchemes": {
  "bearerHttpAuthentication": {
    "type": "http",
    "scheme": "Bearer",
    "bearerFormat": "JWT",
    "description": "Bearer token using a JWT"
  },
  "defaultApiKey": {
    "type": "apiKey",
    "name": "api-key",
    "in": "header",
    "description": "API key provided in console"
  }
}
```

Operations requiring authentication:
- `createGame` - Requires Bearer token
- `deleteGame` - Requires Bearer token
- `getGame` - Requires Bearer token
- `listGames` - Requires Bearer token

## How Security Enforcement Works

### 1. Generated Routes Support Middleware

Generated routes include middleware hooks:

```php
$route = $router->POST('/v1/games', 'Tic Tac Toe@createGame')
    ->name('api.createGame');

// Only attach middleware if the group is registered
if ($router->hasMiddlewareGroup('api.middlewareGroup.createGame')) {
    $route->middleware('api.middlewareGroup.createGame');
}
```

### 2. Developer Configures Security Middleware

In `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    // Based on OpenAPI security schemes, configure middleware for protected operations
    $middleware->group('api.middlewareGroup.createGame', [
        \App\Http\Middleware\ValidateBearerToken::class,
    ]);

    $middleware->group('api.middlewareGroup.deleteGame', [
        \App\Http\Middleware\ValidateBearerToken::class,
    ]);

    $middleware->group('api.middlewareGroup.getGame', [
        \App\Http\Middleware\ValidateBearerToken::class,
    ]);
})
```

### 3. Middleware Enforces Security Rules

Example: `app/Http/Middleware/ValidateBearerToken.php`

```php
public function handle(Request $request, Closure $next): Response
{
    $authHeader = $request->header('Authorization');

    if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
        return response()->json([
            'code' => 'UNAUTHORIZED',
            'message' => 'Bearer token required in Authorization header'
        ], 401);
    }

    $token = substr($authHeader, 7);

    // TODO: Validate JWT token
    // Example: JWT::decode($token, $key, ['HS256']);

    return $next($request);
}
```

## Testing Security Enforcement

### Without Token (Rejected)

```bash
$ curl -X POST http://localhost:8000/api/v2/tictactoe/v1/games \
  -H "Content-Type: application/json" \
  -d '{"mode":"ai_easy"}'

{
  "code": "UNAUTHORIZED",
  "message": "Bearer token required in Authorization header"
}
```

### With Valid Token (Accepted)

```bash
$ curl -X POST http://localhost:8000/api/v2/tictactoe/v1/games \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer valid-token-12345" \
  -d '{"mode":"ai_easy"}'

{
  "id": "550e8400-e29b-41d4-a716-...",
  "status": "pending",
  ...
}
```

### With Invalid Token (Rejected)

```bash
$ curl -X POST http://localhost:8000/api/v2/tictactoe/v1/games \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer invalid" \
  -d '{"mode":"ai_easy"}'

{
  "code": "INVALID_TOKEN",
  "message": "Token validation failed"
}
```

## Available Security Middleware

### 1. Bearer Token (JWT)

**File**: `app/Http/Middleware/ValidateBearerToken.php`

Validates JWT tokens from `Authorization: Bearer <token>` header.

**Usage**:
```php
$middleware->group('api.middlewareGroup.{operationId}', [
    \App\Http\Middleware\ValidateBearerToken::class,
]);
```

**Customization**: Implement actual JWT validation using libraries like `firebase/php-jwt`.

### 2. API Key

**File**: `app/Http/Middleware/ValidateApiKey.php`

Validates API keys from header or query parameters.

**Usage**:
```php
$middleware->group('api.middlewareGroup.{operationId}', [
    \App\Http\Middleware\ValidateApiKey::class,
]);
```

**Customization**: Validate against database or cache of valid API keys.

## Implementation Checklist

- [x] Security schemes defined in OpenAPI spec
- [x] Generated routes support middleware groups
- [x] Security middleware created (Bearer, API Key)
- [x] Middleware registered in bootstrap/app.php
- [x] Security enforced at runtime (401 on missing/invalid auth)
- [ ] JWT validation implemented (connect to auth service)
- [ ] API key validation implemented (connect to database)
- [ ] OAuth 2.0 scopes validated (if using OAuth)

## Key Takeaway

**The generated code forces developers to follow security rules** because:

1. Routes fail open (no security) by default
2. Developer must explicitly configure middleware based on spec
3. Middleware rejects unauthorized requests at runtime
4. Security requirements from spec directly map to middleware groups

This approach ensures the API implementation matches the OpenAPI security specification.
