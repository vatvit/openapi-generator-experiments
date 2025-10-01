# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is an OpenAPI Generator experiments repository for creating and testing custom PHP generators. The project includes:

1. **OpenAPI Generator Tools**: Docker-based code generation for both PHP clients and servers
2. **Laravel API Server**: A complete Laravel 12 API that serves endpoints based on the OpenAPI specification
3. **Server-Side Code Generation**: Generate Laravel controllers, Slim APIs, and custom server implementations

The project uses OpenAPITools/openapi-generator in Docker containers to generate both client and server-side PHP code from OpenAPI specifications, with support for Laravel, Slim, and custom server frameworks.

## Project Structure

```
.
├── petshop-extended.yaml        # Extended OpenAPI specification with custom attributes
├── config/                      # Generator configuration files
│   └── php-laravel-scaffolding-config.json # Laravel scaffolding generator config
├── templates/                   # Custom template directories
│   └── php-laravel-scaffolding/ # Laravel scaffolding templates (controllers, routes, models)
├── scripts/                     # Test scripts
│   ├── test-complete-solution.sh     # Complete end-to-end test
│   └── test-inheritance-workflow.sh  # Test scaffolding inheritance
├── generated/                   # Generated code output
│   └── scaffolding/            # Laravel scaffolding (external library)
│       ├── lib/                # PSR-4 autoloaded library
│       │   ├── Models/         # OpenAPI schema models
│       │   ├── Api/            # API interfaces
│       │   └── Http/Controllers/ # Abstract controllers
│       └── routes.php          # Auto-generated routes
├── laravel-api/                 # Laravel API server
│   ├── app/Http/Controllers/Api/ # Concrete API controllers (extend generated)
│   ├── app/Http/Middleware/    # Operation-specific middleware
│   ├── routes/api.php          # Includes generated routes
│   ├── bootstrap/app.php       # Middleware registration
│   ├── generated/scaffolding/  # Copied from ../generated/scaffolding
│   ├── docker-compose.yml      # Laravel development environment
│   └── composer.json           # PSR-4 autoloading configuration
└── Makefile                    # Make targets for common tasks

## Development Setup

**Docker-Only Environment**: This project uses Docker containers for all development tools and runtimes. No local installation of Node.js, PHP, Python, or other development tools is required - only Docker.

All development commands should be executed through Docker containers:
- Use `docker run` or `docker-compose` for running development tools
- Mount the project directory as a volume for file access
- Consider using development containers or docker-compose for consistent environments

When adding project files, include:
- Dockerfile(s) for development environments
- docker-compose.yml for multi-service setups
- Package configuration files (package.json, requirements.txt, Cargo.toml, etc.)
- Scripts that wrap Docker commands for common tasks

## Common Commands

### Quick Start with Make
```bash
make help                    # Show all available commands

# Complete workflow test
make test-complete          # Run full test: validate spec → generate scaffolding → test endpoints

# Scaffolding generation
make generate-scaffolding   # Generate Laravel scaffolding with abstract controllers and routes

# Utilities
make validate-spec          # Validate OpenAPI specification
make clean                  # Clean generated files
```

### Running the Complete Solution
```bash
# 1. Generate scaffolding from OpenAPI spec
make generate-scaffolding

# 2. Copy generated files to Laravel app (if not using Docker volumes)
cp -r generated/scaffolding laravel-api/generated/

# 3. Start Laravel containers
cd laravel-api && docker-compose up -d

# 4. Run composer dumpautoload to refresh PSR-4 autoloading
docker-compose exec app composer dumpautoload

# 5. Test the endpoints
curl http://localhost:8000/api/v2/pets
curl http://localhost:8000/api/v2/pets?limit=5
```

### Docker Commands
```bash
# Generate Laravel scaffolding with custom templates
docker run --rm -v $(pwd):/local openapitools/openapi-generator-cli generate \
  -i /local/petshop-extended.yaml \
  -g php-laravel \
  -o /local/generated/scaffolding \
  -c /local/config/php-laravel-scaffolding-config.json \
  --template-dir /local/templates/php-laravel-scaffolding

# Extract default Laravel templates for customization
docker run --rm -v $(pwd):/local openapitools/openapi-generator-cli author template \
  -g php-laravel -o /local/templates/php-laravel-default

# Validate OpenAPI spec
docker run --rm -v $(pwd):/local openapitools/openapi-generator-cli validate \
  -i /local/petshop-extended.yaml
```

## Laravel Scaffolding Architecture

### Core Concept: External Library Pattern

The scaffolding is generated as an **external library** that Laravel includes via PSR-4 autoloading. This approach provides:
- **Clean separation** between generated code and application code
- **Easy regeneration** without modifying application files
- **IDE navigation** between app controllers and generated abstract controllers
- **Version control** flexibility (can .gitignore generated code or commit it)

### How It Works

1. **Generation**: OpenAPI Generator creates scaffolding in `generated/scaffolding/`
   - Abstract controllers with validation methods
   - Model classes from OpenAPI schemas
   - Routes file with Laravel route definitions
   - API interfaces

2. **Integration**: Laravel includes scaffolding via PSR-4 autoloading
   ```json
   "autoload": {
       "psr-4": {
           "PetStoreApi\\Scaffolding\\": "generated/scaffolding/lib/"
       }
   }
   ```

3. **Extension**: Application controllers extend generated abstract controllers
   ```php
   use PetStoreApi\Scaffolding\Http\Controllers\DefaultController;

   class PetStoreController extends DefaultController
   {
       public function findPets(Request $request): JsonResponse {
           // Implement business logic
       }
   }
   ```

4. **Service Container Binding**: Routes use string notation resolved via Service Container
   ```php
   // In routes/api.php
   app()->bind('PetStoreApiController', \App\Http\Controllers\Api\PetStoreController::class);

   // In generated routes.php
   $router->GET('/v2/pets', 'PetStoreApiController@findPets')
   ```

### Generated Scaffolding Features

#### Abstract Controllers
- **One abstract method per API operation** with proper type hints
- **Validation methods** with rules from OpenAPI spec
- **PHPDoc comments** with parameter descriptions
- **Path parameter validators** (type-hinted by Laravel routing)

```php
abstract class DefaultController extends Controller
{
    abstract public function findPets(Request $request): JsonResponse;

    protected function findPetsValidationRules(): array
    {
        return [
            'tags' => 'sometimes|array',
            'limit' => 'sometimes|integer',
        ];
    }
}
```

#### Auto-Generated Routes
- **Laravel route syntax** using `$router` variable from Route::group
- **Named routes** with 'api.' prefix (e.g., 'api.findPets')
- **Operation-specific middleware** using operationId as middleware alias
- **Service Container resolution** via string notation

```php
$router->GET('/v2/pets', 'PetStoreApiController@findPets')
    ->name('api.findPets')
    ->middleware('api.operation.findPets');
```

#### Operation-Specific Middleware Groups
Each route uses a middleware group based on its operationId with `api.operation.` prefix. These groups are **empty by default** - you append custom middleware in `bootstrap/app.php` as needed:

```php
->withMiddleware(function (Middleware $middleware): void {
    // Append custom middleware to specific operation groups
    $middleware->appendToGroup('api.operation.findPets', [
        \App\Http\Middleware\CacheResponse::class,
    ]);

    $middleware->appendToGroup('api.operation.addPet', [
        \App\Http\Middleware\ValidateOwnership::class,
        \App\Http\Middleware\LogCreation::class,
    ]);

    $middleware->appendToGroup('api.operation.deletePet', [
        \App\Http\Middleware\RequireAdmin::class,
    ]);

    // Or prepend middleware to run before others
    $middleware->prependToGroup('api.operation.findPetById', [
        \App\Http\Middleware\CheckRateLimit::class,
    ]);
})
```

**Key Benefits**:
- **No template modification needed** - Add/remove middleware without regenerating
- **Multiple middleware per operation** - Append as many as needed
- **Operations can have no middleware** - Groups default to empty
- **Flexible ordering** - Use `appendToGroup` or `prependToGroup`

```php
class OperationMiddleware {
    public function handle(Request $request, Closure $next): Response {
        $route = $request->route();
        $operationId = $route->getName(); // e.g., 'api.findPets'

        match ($operationId) {
            'api.findPets' => $this->handleFindPets($request),
            'api.addPet' => $this->handleAddPet($request),
            default => null,
        };

        return $next($request);
    }
}
```

### Configuration: Customizing Controller Names

Controller name is defined in OpenAPI spec's `info.title`:

```yaml
# petshop-extended.yaml
info:
  title: PetStoreApiController  # Used as controller name in routes
  version: "1.0.0"
```

This makes templates reusable across different API projects without hardcoding controller names.

## Troubleshooting Guide

### Common Issues and Solutions

#### Issue 1: PSR-4 Compliance Errors in Models
**Symptom**: `Class PetStoreApi\Scaffolding\PetStoreApi\Scaffolding\Models\Pet does not comply with psr-4 autoloading standard`

**Cause**: Generated model classes have duplicate namespace in their namespace declaration

**Status**: Known issue in OpenAPI Generator's php-laravel templates - doesn't affect core functionality

**Workaround**: Models are not used in current architecture (controllers work with arrays/JSON). If needed, create custom model templates.

#### Issue 2: Empty README.mustache Causes Generation Exception
**Symptom**: `Could not generate supporting file 'README.mustache'` on first run after deleting generated folder

**Cause**: Empty README.mustache template file

**Solution**: Ensure `templates/php-laravel-scaffolding/README.mustache` has content:
```mustache
# {{packageName}}

{{packageDescription}}

## Generated API Scaffolding
...
```

#### Issue 3: Routes Not Found (404 errors)
**Symptom**: All API endpoints return 404

**Diagnosis**:
```bash
# Check if generated routes file exists in container
docker-compose exec app ls -la generated/scaffolding/

# Check if routes are being included
docker-compose exec app grep "Generated routes file not found" storage/logs/laravel.log
```

**Solutions**:
1. **Ensure files are copied to Laravel app**:
   ```bash
   cp -r generated/scaffolding laravel-api/generated/
   ```

2. **Verify Docker volume mounts**: Check `laravel-api/docker-compose.yml` mounts working directory

3. **Restart containers** after copying files:
   ```bash
   docker-compose restart app
   ```

#### Issue 4: Class Not Found Errors
**Symptom**: `Class "PetStoreApiController" does not exist` or `Class "DefaultController" does not exist`

**Diagnosis**:
```bash
# Check if autoloader knows about the class
docker-compose exec app php -r "var_dump(class_exists('PetStoreApi\Scaffolding\Http\Controllers\DefaultController'));"
```

**Solutions**:
1. **Run composer dumpautoload** after generating scaffolding:
   ```bash
   docker-compose exec app composer dumpautoload
   ```

2. **Verify PSR-4 configuration** in `laravel-api/composer.json`:
   ```json
   "autoload": {
       "psr-4": {
           "PetStoreApi\\Scaffolding\\": "generated/scaffolding/lib/"
       }
   }
   ```

3. **Check class name matches filename** for PSR-4 compliance

#### Issue 5: Middleware Not Working
**Symptom**: Middleware not executing, routes work but no middleware logs

**Diagnosis**:
```bash
# Check middleware registration
docker-compose exec app php artisan route:list --path=v2

# Check Laravel logs for middleware execution
docker-compose exec app tail -f storage/logs/laravel.log
```

**Solutions**:
1. **Verify middleware groups** in `bootstrap/app.php`:
   ```php
   $middleware->appendToGroup('api.operation.findPets', [
       \App\Http\Middleware\OperationMiddleware::class,
   ]);
   ```

2. **Check middleware class exists** at `app/Http/Middleware/OperationMiddleware.php`

3. **Verify route middleware syntax** in generated routes:
   ```php
   ->middleware('api.operation.findPets')  // Correct - middleware group
   // NOT: ->middleware('operation-middleware-group:findPets')
   ```

4. **Remember groups are empty by default** - If you removed all `appendToGroup` calls, middleware won't execute

#### Issue 6: Port 8080 vs 8000
**Symptom**: `curl http://localhost:8080/api/v2/pets` returns connection error

**Cause**: Laravel webserver is on port 8000, not 8080

**Solution**: Check `laravel-api/docker-compose.yml` for actual port mapping:
```yaml
webserver:
  ports:
    - "8000:80"  # Host port 8000 maps to container port 80
```

Use correct port: `curl http://localhost:8000/api/v2/pets`

#### Issue 7: Docker Compose Version Warning
**Symptom**: `the attribute 'version' is obsolete`

**Cause**: Docker Compose v2 deprecates version field

**Solution**: Remove `version: '3.8'` from `docker-compose.yml` files (cosmetic issue, doesn't affect functionality)

### Debugging Workflow

1. **Verify generation succeeded**:
   ```bash
   ls -la generated/scaffolding/lib/Http/Controllers/
   ls -la generated/scaffolding/routes.php
   ```

2. **Check files copied to Laravel**:
   ```bash
   docker-compose exec app ls -la generated/scaffolding/
   ```

3. **Verify autoloading**:
   ```bash
   docker-compose exec app composer dumpautoload
   docker-compose exec app php -r "var_dump(class_exists('PetStoreApi\\\Scaffolding\\\Http\\\Controllers\\\DefaultController'));"
   ```

4. **Test Service Container binding**:
   ```bash
   docker-compose exec app php artisan tinker
   >>> app('PetStoreApiController')
   ```

5. **Check route registration**:
   ```bash
   docker-compose exec app php artisan route:list --path=v2
   ```

6. **Test endpoint**:
   ```bash
   curl -v http://localhost:8000/api/v2/pets
   ```

7. **Check logs**:
   ```bash
   docker-compose exec app tail -50 storage/logs/laravel.log
   ```

## Template Variables
Common variables available in Mustache templates:
- `{{packageName}}` - PHP package name
- `{{invokerPackage}}` - Base namespace
- `{{modelPackage}}` - Model namespace
- `{{apiPackage}}` - API namespace
- `{{appName}}` - Controller name (from OpenAPI info.title)
- `{{classname}}` - Generated class name
- `{{operations}}` - API operations array
- `{{operation}}` - Individual operation
- `{{operationId}}` - Operation ID (e.g., 'findPets')
- `{{httpMethod}}` - HTTP method (GET, POST, DELETE, etc.)
- `{{path}}` - API path (e.g., '/pets')
- `{{basePathWithoutHost}}` - Base path from OpenAPI spec
- `{{models}}` - Data models

See OpenAPI Generator PHP documentation for complete variable list.

## Key Design Decisions

### 1. External Library Pattern
**Decision**: Generate scaffolding as external library included via PSR-4 autoloading

**Reasoning**:
- Clean separation between generated and application code
- Easy regeneration without conflicts
- Follows dependency management best practices
- IDE can navigate relationships

**Alternative Considered**: Generate directly into app/ - rejected due to regeneration conflicts

### 2. String Notation + Service Container
**Decision**: Use `'ControllerName@method'` string notation in routes, resolved via Service Container binding

**Reasoning**:
- Decouples generated routes from concrete controller implementations
- Controller name configurable via OpenAPI spec (info.title)
- Allows different implementations without regenerating routes
- Laravel-idiomatic pattern

**Alternative Considered**: Fully qualified class names - rejected as it would require hardcoding or complex template logic

### 3. Operation-Specific Middleware Groups
**Decision**: Each route uses a middleware group based on operationId with `api.operation.` prefix (e.g., `->middleware('api.operation.findPets')`). Groups are empty by default.

**Reasoning**:
- **No template modification required** - Developers append middleware via `appendToGroup()` in bootstrap/app.php
- **Multiple middleware per operation** - Can add as many middleware as needed to each group
- **Zero middleware overhead** - Operations without custom logic have no middleware (empty group)
- **Flexible ordering** - Use `appendToGroup` or `prependToGroup` as needed
- **Prefix avoids collisions** with other middleware groups in the application

**Previous Approaches**:
- Middleware aliases - rejected because each alias can only map to one class
- `->middleware('operation-middleware-group:findPets')` - rejected as it used parameter instead of groups

### 4. Abstract Controllers with Validation Methods
**Decision**: Generate abstract controllers that application controllers extend

**Reasoning**:
- Type safety via abstract method signatures
- Validation rules available but optional to use
- PHPDoc provides IDE hints from OpenAPI spec
- Clear contract between generated and application code

**Alternative Considered**: Interfaces - rejected as they don't support validation method implementations

### 5. Route File Inclusion via require
**Decision**: Include generated routes via `require` inside `Route::group()`

**Reasoning**:
- Routes can use `$router` variable from closure
- Allows wrapping with shared middleware, prefix, etc.
- File can be conditionally included
- Simple and Laravel-idiomatic

**Alternative Considered**: Service provider - rejected as overkill for simple inclusion
## Important Implementation Notes

### Template File Requirements

All template files in `templates/php-laravel-scaffolding/` MUST have content. Empty template files cause generation exceptions.

**Critical Files**:
- `README.mustache` - Must have full content (not empty)
- `routes.mustache` - Route generation with middleware
- `api_controller.mustache` - Abstract controller generation
- All other `.mustache` files must be complete

### PSR-4 Compliance Requirements

Generated controllers MUST follow PSR-4 naming:
- **Class name must match filename**: `DefaultController` class in `DefaultController.php`
- **Namespace must match directory structure**: `PetStoreApi\Scaffolding\Http\Controllers` for files in `lib/Http/Controllers/`
- **Single namespace declaration**: Avoid duplicate namespace segments

### Middleware Pattern Agreement

**Agreed Pattern**: Each route uses a middleware group based on operationId with `api.operation.` prefix. Groups are empty by default.
```php
->middleware('api.operation.findPets')      // Correct - middleware group
->middleware('api.operation.addPet')        // Correct - middleware group
```

**Usage**: Append custom middleware to groups in `bootstrap/app.php`
```php
$middleware->appendToGroup('api.operation.findPets', [
    \App\Http\Middleware\CacheResponse::class,
]);

$middleware->appendToGroup('api.operation.addPet', [
    \App\Http\Middleware\ValidateOwnership::class,
    \App\Http\Middleware\LogCreation::class,
]);
```

**Rejected Pattern**: Single middleware with parameter
```php
->middleware('operation-middleware-group:findPets')  // WRONG - don't use this
```

**Key Benefits**:
- **Groups default to empty** - No middleware overhead for operations that don't need it
- **Multiple middleware per operation** - Not limited to one class per operation
- **No template changes needed** - Add/remove middleware without regenerating
- **Prefix prevents collisions** with other middleware groups (e.g., web route groups)

### Controller Naming Convention

Controller name comes from OpenAPI spec's `info.title`:
```yaml
info:
  title: PetStoreApiController  # This becomes the controller name in routes
```

This is bound to concrete implementation via Service Container in `routes/api.php`:
```php
app()->bind('PetStoreApiController', \App\Http\Controllers\Api\PetStoreController::class);
```

### File Copying After Generation

After generating scaffolding, ALWAYS:
1. Copy to Laravel app: `cp -r generated/scaffolding laravel-api/generated/`
2. Run composer dumpautoload: `docker-compose exec app composer dumpautoload`
3. Restart containers if needed: `docker-compose restart app`

### Testing After Changes

After modifying templates:
1. Delete generated folder: `rm -rf generated/scaffolding`
2. Regenerate: `make generate-scaffolding`
3. Run complete test: `make test-complete`
4. Verify endpoint works: `curl http://localhost:8000/api/v2/pets`
