# Security Enforcement Analysis

## Your Proposed Idea

**Generate middleware interfaces from OpenAPI securitySchemes, then validate at route registration that middleware groups contain classes implementing the required security interface.**

## Detailed Analysis

### 1. OpenAPI Security Model

From `specs/tictactoe.json`, we have:

**Security Schemes (Global Definitions)**:
```json
{
  "defaultApiKey": {
    "type": "apiKey",
    "name": "api-key",
    "in": "header"
  },
  "bearerHttpAuthentication": {
    "type": "http",
    "scheme": "Bearer",
    "bearerFormat": "JWT"
  },
  "app2AppOauth": {
    "type": "oauth2",
    "flows": {
      "clientCredentials": {
        "tokenUrl": "...",
        "scopes": {"board:read": "Read the board"}
      }
    }
  },
  "user2AppOauth": {
    "type": "oauth2",
    "flows": {
      "authorizationCode": {
        "tokenUrl": "...",
        "scopes": {
          "board:read": "Read the board",
          "board:write": "Write to the board"
        }
      }
    }
  }
}
```

**Operation Security (Per-Operation Requirements)**:
- Some operations require single security scheme: `"security": [{"bearerHttpAuthentication": []}]`
- Some operations allow multiple alternatives (OR): `"security": [{"defaultApiKey": []}, {"app2AppOauth": ["board:read"]}]`
- Some operations have no security requirements (public endpoints)

**Key Insight**: Security arrays are **OR** logic - operation satisfies security if **any one** scheme is validated.

### 2. Your Proposed Implementation

#### Step 1: Generate Security Interfaces

For each `securityScheme`, generate a middleware interface:

```php
namespace TicTacToeApiV2\Scaffolding\Security;

/**
 * Security interface for: bearerHttpAuthentication
 * Type: http
 * Scheme: Bearer
 * Format: JWT
 */
interface BearerHttpAuthenticationInterface
{
    /**
     * Validate Bearer token authentication
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, \Closure $next);
}

/**
 * Security interface for: defaultApiKey
 * Type: apiKey
 * Parameter: api-key
 * Location: header
 */
interface DefaultApiKeyInterface
{
    public function handle($request, \Closure $next);
}

/**
 * Security interface for: app2AppOauth
 * Type: oauth2
 * Flow: clientCredentials
 * Scopes: board:read
 */
interface App2AppOauthInterface
{
    public function handle($request, \Closure $next);

    /**
     * Validate OAuth scopes
     * @param array $requiredScopes
     * @return bool
     */
    public function validateScopes(array $requiredScopes): bool;
}
```

#### Step 2: Routes Validation Logic

In `routes.php`, add runtime validation:

```php
/**
 * POST /games
 * Security: bearerHttpAuthentication
 */
$route = $router->POST('/v1/games', 'Tic Tac Toe@createGame')
    ->name('api.createGame');

// Validate middleware implements required security interface
if ($router->hasMiddlewareGroup('api.middlewareGroup.createGame')) {
    $middlewares = $router->getMiddlewareGroups()['api.middlewareGroup.createGame'];

    // Check at least one middleware implements BearerHttpAuthenticationInterface
    $hasRequiredSecurity = false;
    foreach ($middlewares as $middleware) {
        $class = is_string($middleware) ? $middleware : get_class($middleware);
        if (is_subclass_of($class, \TicTacToeApiV2\Scaffolding\Security\BearerHttpAuthenticationInterface::class)) {
            $hasRequiredSecurity = true;
            break;
        }
    }

    if (!$hasRequiredSecurity) {
        throw new \RuntimeException(
            "Operation 'createGame' requires bearerHttpAuthentication security. " .
            "Middleware group 'api.middlewareGroup.createGame' must contain a class implementing " .
            "\\TicTacToeApiV2\\Scaffolding\\Security\\BearerHttpAuthenticationInterface"
        );
    }

    $route->middleware('api.middlewareGroup.createGame');
} else {
    // No middleware group defined - fail secure
    throw new \RuntimeException(
        "Operation 'createGame' requires security (bearerHttpAuthentication) but " .
        "middleware group 'api.middlewareGroup.createGame' is not defined"
    );
}
```

#### Step 3: Developer Implementation

Developer must implement interfaces:

```php
namespace App\Http\Middleware;

use TicTacToeApiV2\Scaffolding\Security\BearerHttpAuthenticationInterface;

class ValidateBearerToken implements BearerHttpAuthenticationInterface
{
    public function handle($request, \Closure $next)
    {
        // Implementation...
    }
}
```

### 3. Feasibility Analysis

#### ✅ Advantages

1. **Compile-Time Type Safety**: Middleware MUST implement correct interface
2. **Runtime Validation**: Routes fail to load if security not properly implemented
3. **Clear Documentation**: Interfaces document security requirements
4. **IDE Support**: Auto-completion and type hints for security middleware
5. **Fail-Secure Default**: Operations with security requirements cannot be exposed without proper middleware

#### ❌ Challenges

1. **OR Logic Complexity**:
   - Spec: `"security": [{"apiKey": []}, {"bearer": []}]` means "apiKey OR bearer"
   - Validation must check if ANY required interface is implemented
   - Complex logic for multiple alternatives

2. **OAuth Scopes**:
   - Different operations require different scopes for same OAuth scheme
   - Example: `get-board` needs `["board:read"]`, `put-square` needs `["board:write"]`
   - Interface must support dynamic scope validation
   - Middleware needs to know which scopes to validate per-operation

3. **Route File Execution Context**:
   - Route files execute during application bootstrap
   - Throwing exceptions during route registration breaks the entire app
   - Validation happens EVERY request (performance impact)
   - Better to validate once during app boot or route:cache

4. **Laravel Middleware Architecture**:
   - `hasMiddlewareGroup()` and `getMiddlewareGroups()` work at registration time
   - Middleware can be closures, not just classes
   - Middleware can be registered as aliases, making class detection harder

5. **Missing Variables in php-laravel Generator**:
   - No `{{hasAuthMethods}}`, `{{authMethods}}` variables available
   - Would need to parse OpenAPI spec manually in template OR modify generator
   - Cannot use mustache conditionals for security logic

### 4. Alternative Approaches

#### Alternative 1: Artisan Command Validation (Recommended)

Generate an artisan command that validates security implementation:

```bash
php artisan openapi:validate-security
```

**Implementation**:
```php
// Generated: app/Console/Commands/ValidateOpenapiSecurity.php
class ValidateOpenapiSecurity extends Command
{
    protected $signature = 'openapi:validate-security';

    public function handle()
    {
        $violations = [];

        // createGame requires BearerHttpAuthenticationInterface
        if (app()->hasMiddlewareGroup('api.middlewareGroup.createGame')) {
            $middlewares = app()->getMiddlewareGroups()['api.middlewareGroup.createGame'];
            if (!$this->hasInterface($middlewares, BearerHttpAuthenticationInterface::class)) {
                $violations[] = "createGame: Missing BearerHttpAuthenticationInterface";
            }
        } else {
            $violations[] = "createGame: Middleware group not defined (requires bearerHttpAuthentication)";
        }

        // ... check all operations

        if (count($violations) > 0) {
            $this->error("Security violations found:");
            foreach ($violations as $v) {
                $this->error("  - $v");
            }
            return 1;
        }

        $this->info("All security requirements satisfied!");
        return 0;
    }
}
```

**Advantages**:
- Validation happens on-demand, not every request
- Can be added to CI/CD pipeline
- Clear error messages
- No runtime performance impact

#### Alternative 2: Service Provider Registration

Generate a service provider that validates during app boot:

```php
// Generated: TicTacToeApiV2/Scaffolding/SecurityServiceProvider.php
class SecurityServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (!app()->routesAreCached()) {
            $this->validateSecurityRequirements();
        }
    }

    private function validateSecurityRequirements()
    {
        // Same validation logic as Alternative 1
        // Throw exceptions during development
        // Log warnings in production
    }
}
```

**Advantages**:
- Automatic validation every app boot
- Can be disabled in production
- Fails early before requests are handled

#### Alternative 3: Middleware Factory Pattern

Generate a factory that creates validated middleware groups:

```php
// Generated: TicTacToeApiV2/Scaffolding/Security/SecurityMiddlewareFactory.php
class SecurityMiddlewareFactory
{
    public static function createGame(): array
    {
        return self::requireBearerAuth();
    }

    public static function getBoard(): array
    {
        // OR logic: apiKey OR oauth
        return self::requireAny([
            self::requireApiKey('api-key', 'header'),
            self::requireOAuth('app2AppOauth', ['board:read'])
        ]);
    }

    private static function requireBearerAuth(): array
    {
        $middleware = config('openapi.security.bearerHttpAuthentication');
        if (!$middleware || !is_subclass_of($middleware, BearerHttpAuthenticationInterface::class)) {
            throw new \RuntimeException("Bearer auth middleware not configured");
        }
        return [$middleware];
    }
}
```

**Usage in bootstrap/app.php**:
```php
use TicTacToeApiV2\Scaffolding\Security\SecurityMiddlewareFactory;

$middleware->group('api.middlewareGroup.createGame',
    SecurityMiddlewareFactory::createGame()
);
```

**Advantages**:
- Type-safe middleware creation
- Centralized security logic
- OR logic handled in factory
- Fails at configuration time, not runtime

#### Alternative 4: Auto-Generate Middleware Groups (Simple, Recommended)

Don't validate interfaces. Instead, auto-generate the middleware group configuration:

```php
// Generated: config/openapi-security.php
return [
    'securitySchemes' => [
        'bearerHttpAuthentication' => \App\Http\Middleware\ValidateBearerToken::class,
        'defaultApiKey' => \App\Http\Middleware\ValidateApiKey::class,
        'app2AppOauth' => \App\Http\Middleware\ValidateOAuthClientCredentials::class,
        'user2AppOauth' => \App\Http\Middleware\ValidateOAuthAuthorizationCode::class,
    ],

    'operations' => [
        'createGame' => [
            'required' => ['bearerHttpAuthentication'],
            'alternatives' => [] // OR logic: any one of these
        ],
        'getBoard' => [
            'required' => [],
            'alternatives' => ['defaultApiKey', 'app2AppOauth'] // apiKey OR oauth
        ],
    ],
];
```

**Generated routes.php**:
```php
$securityConfig = config('openapi-security.operations.createGame');
$middlewares = [];

// Add required security (AND logic)
foreach ($securityConfig['required'] as $scheme) {
    $class = config("openapi-security.securitySchemes.$scheme");
    if (!$class) {
        throw new \RuntimeException("Security scheme '$scheme' not configured for operation 'createGame'");
    }
    $middlewares[] = $class;
}

// Add alternatives (OR logic) - handled by custom middleware
if (!empty($securityConfig['alternatives'])) {
    $middlewares[] = new \TicTacToeApiV2\Scaffolding\Security\AlternativeSecurityMiddleware(
        $securityConfig['alternatives']
    );
}

$router->POST('/v1/games', 'Tic Tac Toe@createGame')
    ->middleware($middlewares)
    ->name('api.createGame');
```

**Advantages**:
- Simple configuration
- Clear mapping from spec to middleware
- Auto-generated, no manual setup
- OR logic handled by special middleware
- Developer just needs to implement middleware classes

### 5. Recommended Approach

**Combination of Alternative 1 + Alternative 4**:

1. **Auto-generate middleware configuration** from OpenAPI security schemes
2. **Auto-attach middleware** to routes based on operation security requirements
3. **Provide validation command** to verify implementation: `php artisan openapi:validate-security`
4. **Optional: Generate skeleton middleware** with interfaces for type safety

This provides:
- ✅ Automatic security enforcement (routes won't work without middleware)
- ✅ Validation tool for CI/CD
- ✅ Clear configuration mapping
- ✅ Type-safe interfaces (optional)
- ✅ Handles OR logic correctly
- ✅ No runtime performance impact

### 6. Implementation Complexity

| Approach | Complexity | Enforcement Level | Developer Experience |
|----------|-----------|-------------------|---------------------|
| **Your Idea (Interface Validation)** | High | Runtime (every request) | Complex OR logic |
| **Alt 1: Artisan Command** | Low | On-demand | Great (clear errors) |
| **Alt 2: Service Provider** | Medium | Boot time | Good (automatic) |
| **Alt 3: Factory Pattern** | Medium | Config time | Complex setup |
| **Alt 4: Auto-Config** | Low | Route registration | Excellent (automatic) |

### 7. Proof of Concept: Your Idea

Here's how your idea would look in generated code:

**Generated Interfaces**:
```php
// lib/Security/BearerHttpAuthenticationInterface.php
interface BearerHttpAuthenticationInterface {
    public function handle($request, \Closure $next);
}

// lib/Security/DefaultApiKeyInterface.php
interface DefaultApiKeyInterface {
    public function handle($request, \Closure $next);
}
```

**Generated Routes with Validation**:
```php
// For simple case (single security scheme)
$route = $router->POST('/v1/games', 'Tic Tac Toe@createGame');

if (!$router->hasMiddlewareGroup('api.middlewareGroup.createGame')) {
    throw new \RuntimeException(
        "Security required: Operation 'createGame' requires middleware group " .
        "'api.middlewareGroup.createGame' implementing BearerHttpAuthenticationInterface"
    );
}

$middlewares = app('router')->getMiddlewareGroups()['api.middlewareGroup.createGame'] ?? [];
$hasValidSecurity = false;

foreach ($middlewares as $mw) {
    if (is_subclass_of($mw, \TicTacToeApiV2\Scaffolding\Security\BearerHttpAuthenticationInterface::class)) {
        $hasValidSecurity = true;
        break;
    }
}

if (!$hasValidSecurity) {
    throw new \RuntimeException(
        "Invalid security: Middleware group 'api.middlewareGroup.createGame' must " .
        "contain middleware implementing BearerHttpAuthenticationInterface"
    );
}

$route->middleware('api.middlewareGroup.createGame');
```

**For OR logic (more complex)**:
```php
// get-board: defaultApiKey OR app2AppOauth
$route = $router->GET('/v1/games/{gameId}/board', 'Tic Tac Toe@getBoard');

if (!$router->hasMiddlewareGroup('api.middlewareGroup.getBoard')) {
    throw new \RuntimeException(
        "Security required: Operation 'getBoard' requires authentication " .
        "(defaultApiKey OR app2AppOauth)"
    );
}

$middlewares = app('router')->getMiddlewareGroups()['api.middlewareGroup.getBoard'] ?? [];
$hasValidSecurity = false;

// Check if ANY alternative is satisfied
$requiredInterfaces = [
    \TicTacToeApiV2\Scaffolding\Security\DefaultApiKeyInterface::class,
    \TicTacToeApiV2\Scaffolding\Security\App2AppOauthInterface::class,
];

foreach ($middlewares as $mw) {
    foreach ($requiredInterfaces as $interface) {
        if (is_subclass_of($mw, $interface)) {
            $hasValidSecurity = true;
            break 2;
        }
    }
}

if (!$hasValidSecurity) {
    throw new \RuntimeException(
        "Invalid security: Middleware group 'api.middlewareGroup.getBoard' must " .
        "contain middleware implementing one of: DefaultApiKeyInterface, App2AppOauthInterface"
    );
}

$route->middleware('api.middlewareGroup.getBoard');
```

### 8. Key Questions for Your Approach

1. **Exception Handling**: Routes file is included during boot. Throwing exceptions breaks the app. Should we:
   - Throw exceptions (fail-secure, but breaks app if misconfigured)?
   - Log warnings (app works but insecure)?
   - Only validate in development environment?

2. **Performance**: Validation happens every request when routes are loaded. Should we:
   - Cache validation results?
   - Only validate when routes are not cached?
   - Move validation to artisan command?

3. **OR Logic**: How to handle operations that accept multiple security alternatives?
   - Require middleware implements ANY interface?
   - Require middleware implements ALL interfaces?
   - Special middleware that tries alternatives?

4. **OAuth Scopes**: How to validate scope requirements?
   - Interface method: `validateScopes(['board:read'])`?
   - Separate middleware per scope combination?
   - Scope validation in route logic?

## Conclusion

**Your idea is theoretically sound but has practical challenges**:

✅ **Works well for**: Single security scheme per operation
❌ **Complex for**: OR logic, OAuth scopes, error handling

**Recommended Hybrid Approach**:
1. Generate security interfaces (for type safety)
2. Auto-generate middleware configuration (Alternative 4)
3. Provide validation command (Alternative 1)
4. Let developers implement interfaces

This gives strong enforcement without runtime overhead or complex validation logic in routes.
