# CLAUDE.md

Instructions for Claude Code when working with this repository.

## Project Overview

OpenAPI Generator experiments repository for generating Laravel server-side server from OpenAPI specifications.

**Current Solution (V2)**: Uses OpenAPI Generator + post-processing to create clean, deduplicated server.

## Quick Reference

```bash
make help                       # Show all commands
make generate-server-v2    # Generate both PetStore and TicTacToe APIs
make test-complete-v2           # Run complete test suite
```

## Project Structure

```
specs/                          # OpenAPI specifications (source of truth)
├── petshop-extended.yaml
└── tictactoe.json

config-v2/                      # Generator configs (one per spec)
├── petshop-server-config.json
└── tictactoe-server-config.json

templates/php-laravel-server-v2/  # Mustache templates

scripts/
├── merge-controllers-simple.php    # Post-processor: merges tag-based controllers
└── remove-tags.sh                  # Pre-processor: removes tags from spec

laravel-api/
├── generated-v2/                      # Generated server (external libraries)
│   ├── petstore/lib/                 # PetStoreApiV2\Server namespace
│   └── tictactoe/lib/                # TicTacToeApiV2\Server namespace
├── app/Handlers/V2/                   # Business logic implementations
├── app/Http/Middleware/               # Security middleware
└── bootstrap/app.php                  # DI bindings and route registration
```

## Architecture: External Library Pattern

Generated server = **external library** included via PSR-4 autoloading.

### Key Components

1. **API Interfaces** (`lib/Api/DefaultApiInterface.php`)
   - Handler interfaces for dependency injection
   - Response interfaces for type-safe responses
   - Response classes for each HTTP status code

2. **Abstract Controllers** (`lib/Http/Controllers/DefaultController.php`)
   - Abstract methods (one per operation)
   - Validation methods with rules from OpenAPI spec
   - PHPDoc from spec descriptions

3. **Routes** (`routes.php`)
   - Laravel route definitions
   - Conditional middleware application
   - Uses `info.title` from spec as controller name
   - **Auto-validates security middleware** (embedded at end of file)

4. **Security Interfaces** (`lib/Security/SecurityInterfaces.php`)
   - Generated via templates from OpenAPI security schemes
   - One interface per security scheme (bearerHttpAuthentication, defaultApiKey, etc.)
   - Implemented by Laravel middleware for type safety

5. **Security Validator** (`lib/Security/SecurityValidator.php`)
   - Generated via templates to validate middleware configuration
   - Checks that middleware implements correct interfaces
   - **Automatically called from routes.php** (no manual setup required)

### Integration Flow

```
OpenAPI Spec → Generator → Post-Processor → External Library
                                                    ↓
Laravel App ← PSR-4 Autoload ← Composer ← Generated Files
```

## Generation Workflow

### Standard Flow (Both APIs)
```bash
make generate-server
```

This runs for each spec:
1. **Pre-process**: Remove tags from spec (for TicTacToe)
2. **Generate**: Run OpenAPI Generator with custom templates
   - Generates controllers, models, routes
   - Generates security interfaces (via `SecurityInterfaces.php.mustache`)
   - Generates security validator (via `SecurityValidator.php.mustache`)
   - **Embeds validation code in routes.php** (via `routes.mustache`)
3. **Post-process**:
   - Merge duplicate controllers (for PetStore only)

### Individual APIs
```bash
make generate-petshop-v2     # PetStore only
make generate-tictactoe-v2   # TicTacToe only
```

## Multiple Specs Setup

Each spec has:
- **Unique namespace**: `PetStoreApiV2\Server` vs `TicTacToeApiV2\Server`
- **Separate directory**: `generated-v2/petstore/` vs `generated-v2/tictactoe/`
- **Own config file**: Different `invokerPackage` per spec

See [MULTI-SPEC-SETUP.md](MULTI-SPEC-SETUP.md) for details.

## Key Design Decisions

### 1. Post-Processing for Tag Duplication

**Problem**: Operations with multiple tags create duplicate methods across controllers.

**Solution**: Post-processing script merges tag-based controllers into single `DefaultController`.

See [TAG_DUPLICATION_SOLUTION.md](TAG_DUPLICATION_SOLUTION.md) for full analysis.

### 2. Conditional Middleware

Routes check if middleware group exists before applying:

```php
if ($router->hasMiddlewareGroup('api.middlewareGroup.getBoard')) {
    $route->middleware('api.middlewareGroup.getBoard');
}
```

**Benefits**: Zero overhead for operations without middleware, no errors if groups undefined.

### 3. Security Interfaces via Post-Processing

OpenAPI Generator doesn't create security interfaces, so we create them via Makefile post-processing.

### 4. Handler-Based Dependency Injection

Controllers inject handler interfaces, business logic lives in separate handler classes:

```php
// Controller receives handler via DI
public function __construct(private readonly GetBoardHandlerInterface $handler) {}

// Calls handler with validated input
return $this->handler->getBoard($gameId);
```

### 5. Type-Safe Response Objects

Each operation returns response interface, handlers return concrete response classes:

```php
// Handler returns typed response
return new GetBoard200Response($board);

// Response classes enforce HTTP status codes and structure
```

## Security Validation (Fully Automatic)

The build process automatically generates and integrates security validation - **no manual setup required**.

### What Gets Generated

1. **Security Interfaces** (`lib/Security/SecurityInterfaces.php`)
   - One interface per security scheme in OpenAPI spec
   - Example: `bearerHttpAuthenticationInterface`, `defaultApiKeyInterface`

2. **Security Validator** (`lib/Security/SecurityValidator.php`)
   - Validates middleware configuration at runtime
   - Checks that middleware implements correct interfaces
   - Provides clear error messages

3. **Validation Code** (embedded in `routes.php`)
   - Automatically added to end of each API's routes file
   - Calls `SecurityValidator::validateMiddleware($router)`
   - Only runs when `APP_DEBUG=true`

### How It Works (100% Automatic)

When routes are loaded (in debug mode):
1. `routes.php` is included from `bootstrap/app.php`
2. All routes are registered
3. **Validation code at end of routes.php executes automatically**
4. Calls `SecurityValidator::validateMiddleware($router)`
5. Checks that:
   - Secured operations have middleware groups defined
   - Middleware implements the correct security interfaces
6. Logs errors if validation fails (doesn't break application by default)

**Example validation error** (logged to error_log):
```
Security middleware validation failed for TicTacToeApiV2\Server:
  - Operation 'createGame' requires middleware implementing: TicTacToeApiV2\Server\Security\bearerHttpAuthenticationInterface
  - Operation 'getBoard' requires authentication but middleware group 'api.middlewareGroup.getBoard' is not registered
```

### Making Validation Fatal (Optional)

By default, validation logs errors but doesn't break the application. To make validation failures throw exceptions, edit the generated `routes.php`:

```php
// Uncomment this line to make validation failures fatal:
// throw $e;
```

### Disabling Validation

Validation automatically skips in production (`APP_DEBUG=false`). To disable entirely, remove the validation code from `routes.mustache` template.

## Template Variables Reference

Common Mustache variables:
- `{{invokerPackage}}` - Base namespace (e.g., `TicTacToeApiV2\Server`)
- `{{appName}}` - Controller name from `info.title`
- `{{operationId}}` - Operation identifier
- `{{httpMethod}}` - GET, POST, DELETE, etc.
- `{{path}}` - API path
- `{{hasAuthMethods}}` - Boolean: operation requires auth
- `{{authMethods}}` - Array of security schemes

See OpenAPI Generator docs for complete list.

## Important Rules

- ✅ **Do not modify specs** without approval (source of truth)
- ✅ **Do not modify generated code** directly (modify templates and regenerate)
- ✅ **Do not commit/push** without approval
- ✅ **Do not create scripts** without asking first
- ✅ **Do not install local tools** (Docker-only environment)
- ✅ **Do not create post-processing scripts** - all logic should be in templates when possible; tell user about fundamental restrictions instead

## Docker-Only Environment

All commands run in Docker containers:
```bash
docker run --rm -v $(pwd):/local openapitools/openapi-generator-cli generate ...
docker-compose exec app composer dumpautoload
```

No local PHP, Composer, or other tools required.

## Testing

```bash
make test-complete-v2          # Full test: validate → generate → test endpoints
make test-petshop-v2          # Test PetStore endpoints
make test-tictactoe-v2        # Test TicTacToe endpoints
```

## Common Issues

See [TROUBLESHOOTING.md](TROUBLESHOOTING.md) and [KNOWN_ISSUES.md](KNOWN_ISSUES.md) for detailed troubleshooting.

**Quick fixes:**
- Routes returning 404 → `docker-compose restart app`
- Class not found → `docker-compose exec app composer dumpautoload`
- Security interface missing → Run appropriate make target (includes post-processing)

## Further Reading

- [MULTI-SPEC-SETUP.md](MULTI-SPEC-SETUP.md) - Multiple API specs configuration
- [TAG_DUPLICATION_SOLUTION.md](TAG_DUPLICATION_SOLUTION.md) - Tag duplication analysis and solution
- [SOLUTIONS_COMPARISON.md](SOLUTIONS_COMPARISON.md) - V1 vs V2 comparison
- [SECURITY.md](SECURITY.md) - Security implementation details
- [TROUBLESHOOTING.md](TROUBLESHOOTING.md) - Common issues and fixes
- [KNOWN_ISSUES.md](KNOWN_ISSUES.md) - Limitations and workarounds
