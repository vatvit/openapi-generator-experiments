# Laravel OpenAPI Server Generator (V2)

Generate clean, production-ready Laravel server-side libraries from OpenAPI 3.x specifications.

## Overview

This generator creates a Laravel server library as an **external library** that can be integrated into any Laravel application via PSR-4 autoloading. The generated code follows Laravel best practices and includes:

- **Type-safe controllers** with handler-based dependency injection
- **Request validation** from OpenAPI schema constraints
- **Security interfaces** for middleware implementation
- **Response classes** for each HTTP status code
- **API routes** with conditional middleware support
- **Automatic security validation** (in debug mode)

**⚠️ Important:** Always remove tags from your OpenAPI spec before generation to avoid duplicate interface files and dead code. See [Tag Removal](#tag-removal-strongly-recommended) section below.

## Architecture

Generated library structure:
```
generated/
├── lib/
│   ├── Api/                      # Handler interfaces
│   ├── Http/
│   │   ├── Controllers/          # Abstract controllers
│   │   └── Responses/            # Response classes
│   ├── Models/                   # Data models
│   ├── Security/
│   │   ├── SecurityInterfaces.php    # Security scheme interfaces
│   │   └── SecurityValidator.php     # Middleware validator
│   └── FormRequest/              # Validation requests
└── routes.php                    # Laravel route definitions
```

## Requirements

- **Docker** - For running OpenAPI Generator
- **jq** - JSON processor (for `generate-no-tags` target)
- **yq** - YAML processor (for `generate-no-tags` target with YAML specs)

## Quick Start

### 1. Remove Tags from OpenAPI Spec (Recommended)

If your OpenAPI spec has tags on operations, remove them first:

```bash
./remove-tags.sh /path/to/openapi.yaml /path/to/openapi-no-tags.yaml
```

This prevents duplicate interface files and ensures clean code generation. See [Tag Removal](#tag-removal-strongly-recommended) for details.

### 2. Create Configuration File

Create a JSON configuration file (e.g., `config.json`):

```json
{
  "invokerPackage": "MyApi\\Server",
  "modelPackage": "Models",
  "apiPackage": "Api",
  "appName": "MyApiController",
  "hideGenerationTimestamp": true,
  "withGeneratorVersionComment": false,
  "skipOperationExample": true,
  "srcBasePath": "lib",
  "variableNamingConvention": "camelCase",
  "files": {
    "SecurityInterfaces.php.mustache": {
      "destinationFilename": "lib/Security/SecurityInterfaces.php"
    },
    "SecurityValidator.php.mustache": {
      "destinationFilename": "lib/Security/SecurityValidator.php"
    }
  }
}
```

### 3. Generate Server

Run the OpenAPI Generator with Docker (use the no-tags spec):

```bash
docker run --rm \
  -v $(pwd):/local \
  -v $(dirname $(realpath /path/to/openapi-no-tags.yaml)):/specs \
  -v $(dirname $(realpath config.json)):/config \
  openapitools/openapi-generator-cli generate \
  -i /specs/openapi-no-tags.yaml \
  -g php-laravel \
  -o /local/generated \
  -c /config/config.json \
  --template-dir /local/openapi-generator-server-laravel
```

**Parameters:**
- `-i` - Input OpenAPI specification file
- `-g` - Generator name (php-laravel)
- `-o` - Output directory for generated code
- `-c` - Configuration file
- `--template-dir` - Path to this template directory

### 4. Integrate with Laravel

**composer.json** - Add PSR-4 autoload mapping AND files section:
```json
{
  "autoload": {
    "psr-4": {
      "MyApi\\Server\\": "generated/lib/"
    },
    "files": [
      "generated/lib/Api/DefaultApiInterface.php"
    ]
  }
}
```

**Important:** The `files` section is **required** because `DefaultApiInterface.php` contains multiple classes (handler interfaces, response interfaces, and response classes) that don't follow PSR-4 naming conventions. Without this, PHP's autoloader won't be able to find these classes.

#### Why the "files" section is needed

The generated `DefaultApiInterface.php` file contains multiple classes:
- Main API interface (e.g., `DefaultApiInterface`)
- Response interfaces per operation (e.g., `CreateGameResponseInterface`)
- Response classes per HTTP status code (e.g., `CreateGame200Response`, `CreateGame404Response`)
- Handler interfaces per operation (e.g., `CreateGameHandlerInterface`)

**PSR-4 autoloading expects ONE class per file** with the filename matching the class name. Since `DefaultApiInterface.php` contains many classes with different names, PSR-4 can't find them automatically.

The `files` section in composer.json forces eager loading of this file on every request, making all classes available.

**bootstrap/app.php** - Load generated routes:
```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            require base_path('generated/routes.php');
        }
    )
    // ...
```

Run `composer dumpautoload` and restart your Laravel application.

## Configuration Options

### Required Options

- **`invokerPackage`** - Base namespace (e.g., `"MyApi\\Server"`)
- **`modelPackage`** - Models namespace relative to invokerPackage
- **`apiPackage`** - API interfaces namespace relative to invokerPackage
- **`appName`** - Controller name (used in routes, typically from `info.title` in OpenAPI spec)

### Recommended Options

- **`hideGenerationTimestamp`** - Set to `true` for clean generated code
- **`withGeneratorVersionComment`** - Set to `false` to remove version comments
- **`skipOperationExample`** - Set to `true` for cleaner output
- **`srcBasePath`** - Set to `"lib"` for standard structure
- **`variableNamingConvention`** - Set to `"camelCase"` for Laravel conventions

### Security Files Configuration

Always include this to generate security interfaces:

```json
{
  "files": {
    "SecurityInterfaces.php.mustache": {
      "destinationFilename": "lib/Security/SecurityInterfaces.php"
    },
    "SecurityValidator.php.mustache": {
      "destinationFilename": "lib/Security/SecurityValidator.php"
    }
  }
}
```

## Tag Removal (STRONGLY RECOMMENDED)

### Why Remove Tags?

**IMPORTANT:** If your OpenAPI spec uses tags on operations, you should **always** remove them before generation. Here's why:

When operations have tags, OpenAPI Generator creates:
- **Multiple controller files** (one per tag: `AdminController`, `SearchController`, etc.)
- **Multiple API interface files** with **duplicate class definitions**
- **Duplicate handler interfaces, response interfaces, and response classes** across files

**Example Problem:**
- With tags: `PetsApiInterface.php`, `SearchApiInterface.php`, `InventoryApiInterface.php` (12 files total)
- Both `PetsApiInterface.php` AND `SearchApiInterface.php` define `FindPetsHandlerInterface` (duplicates!)
- Only ONE file can be loaded in composer.json to avoid "class already defined" errors
- Results in dead code and confusion

**Solution:**
- Without tags: Single `DefaultApiInterface.php` with all interfaces (1 file)
- No duplicates, clean structure, all classes properly defined

### Using the remove-tags.sh script

```bash
# Remove tags from spec
./remove-tags.sh /path/to/openapi.yaml /path/to/openapi-no-tags.yaml

# Then generate with the modified spec
docker run --rm \
  -v $(pwd):/local \
  -v $(dirname $(realpath /path/to/openapi-no-tags.yaml)):/specs \
  -v $(dirname $(realpath config.json)):/config \
  openapitools/openapi-generator-cli generate \
  -i /specs/openapi-no-tags.yaml \
  -g php-laravel \
  -o /local/generated \
  -c /config/config.json \
  --template-dir /local/openapi-generator-server-laravel
```

**What remove-tags.sh does:**
1. Removes all tags from operations in the spec
2. Supports both JSON and YAML formats
3. Creates a modified spec file
4. Generates a single `DefaultController` with all operations
5. Generates a single `DefaultApiInterface.php` with all interfaces (no duplicates!)

**Requirements:** `jq` (for JSON) and `yq` (for YAML)

**Result:** Clean, deduplicated code with one controller and one API interface file.

## Template Customization

### Available Templates

- **`routes.mustache`** - Route definitions
- **`api_controller.mustache`** - Controller classes
- **`SecurityInterfaces.php.mustache`** - Security interfaces
- **`SecurityValidator.php.mustache`** - Security validator
- **`operation_handler_interface.mustache`** - Handler interfaces
- **`operation_response_interface.mustache`** - Response interfaces
- **`operation_response_classes.mustache`** - Response classes
- **`model_generic.mustache`** - Data models
- **`model_enum.mustache`** - Enum models
- **`form_request.mustache`** - Validation requests

### Common Template Variables

#### Operation Variables
- `{{operationId}}` - Operation identifier (camelCase)
- `{{httpMethod}}` - HTTP method (GET, POST, PUT, DELETE, etc.)
- `{{path}}` - API endpoint path
- `{{summary}}` - Operation summary from spec
- `{{description}}` - Operation description from spec

#### Security Variables
- `{{hasAuthMethods}}` - Boolean: operation requires authentication
- `{{authMethods}}` - Array of security schemes for the operation
- `{{authMethods.0.name}}` - Security scheme name
- `{{authMethods.0.type}}` - Security type (apiKey, http, oauth2, etc.)

#### Package Variables
- `{{invokerPackage}}` - Base namespace (e.g., `MyApi\Server`)
- `{{appName}}` - Controller name from config/spec
- `{{modelPackage}}` - Models namespace
- `{{apiPackage}}` - API interfaces namespace

#### Parameter Variables
- `{{allParams}}` - All parameters (path, query, body)
- `{{pathParams}}` - Path parameters only
- `{{queryParams}}` - Query parameters only
- `{{bodyParams}}` - Request body parameters
- `{{hasParams}}` - Boolean: operation has parameters

#### Validation Variables
- `{{required}}` - Boolean: parameter is required
- `{{minimum}}` - Minimum value constraint
- `{{maximum}}` - Maximum value constraint
- `{{pattern}}` - Regex pattern constraint
- `{{minLength}}` - Minimum length constraint
- `{{maxLength}}` - Maximum length constraint

### Customizing Templates

1. Copy the template you want to modify
2. Edit the Mustache template
3. Regenerate using the docker command

Example - modify controller structure:
```bash
cp api_controller.mustache api_controller.mustache.backup
# Edit api_controller.mustache
# Then run the generator docker command (see Quick Start section)
```

## Security Implementation

### 1. Generated Security Interfaces

The generator creates one interface per security scheme in your OpenAPI spec:

```php
interface bearerHttpAuthenticationInterface
{
    public function handle(Request $request, Closure $next): Response;
}
```

### 2. Implement Middleware

Create Laravel middleware that implements the generated interfaces:

```php
namespace App\Http\Middleware;

use MyApi\Server\Security\bearerHttpAuthenticationInterface;
use Closure;
use Illuminate\Http\Request;

class ValidateBearerToken implements bearerHttpAuthenticationInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');
        if (!str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // TODO: Validate JWT token

        return $next($request);
    }
}
```

### 3. Register Middleware

In `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'api.middlewareGroup.createGame' => \App\Http\Middleware\ValidateBearerToken::class,
    ]);
})
```

### 4. Automatic Validation

Security validation runs automatically in debug mode (`APP_DEBUG=true`) when routes are loaded. It checks:
- Middleware groups are registered for secured operations
- Middleware implements correct security interfaces

Validation errors are logged, not thrown (by default). To make validation fatal, edit `routes.php` and uncomment the `throw $e;` line.

## Handler Implementation

Controllers inject handler interfaces. You implement handlers in your Laravel app:

```php
namespace App\Handlers\V2;

use MyApi\Server\Api\CreateGameHandlerInterface;
use MyApi\Server\Http\Responses\CreateGame200Response;

class CreateGameHandler implements CreateGameHandlerInterface
{
    public function createGame(string $mode): CreateGame200Response
    {
        // Your business logic here
        $game = Game::create(['mode' => $mode]);

        return new CreateGame200Response([
            'id' => $game->id,
            'mode' => $game->mode,
            'status' => 'active'
        ]);
    }
}
```

Register handler in `bootstrap/app.php`:

```php
$app->singleton(
    \MyApi\Server\Api\CreateGameHandlerInterface::class,
    \App\Handlers\V2\CreateGameHandler::class
);
```

## Troubleshooting

### Routes return 404
- Restart Laravel application
- Check that routes are loaded in `bootstrap/app.php`
- Verify `composer dumpautoload` was run

### Class not found
- Run `composer dumpautoload`
- Check PSR-4 autoload mapping in `composer.json`
- Verify namespace in config matches directory structure

### Security validation errors
- Check middleware implements correct interface
- Verify middleware is registered with correct alias
- Review `storage/logs/laravel.log` for details

### Generation fails
- Validate OpenAPI spec: `make validate-spec SPEC_FILE=...`
- Check Docker is running
- Verify all required config options are present

## Advanced Usage

### Multiple API Specifications

To generate multiple APIs, repeat the generation process with different configurations:

```bash
# Generate first API (remove tags first)
./remove-tags.sh openapi.yaml openapi-no-tags.yaml
docker run --rm \
  -v $(pwd):/local \
  openapitools/openapi-generator-cli generate \
  -i /local/openapi-no-tags.yaml \
  -g php-laravel \
  -o /local/generated/myapi \
  -c /local/config.json \
  --template-dir /local/openapi-generator-server-laravel
```

**composer.json** - Add each generated API:
```json
{
  "autoload": {
    "psr-4": {
      "MyApi\\Server\\": "generated/myapi/lib/"
    },
    "files": [
      "generated/myapi/lib/Api/DefaultApiInterface.php"
    ]
  }
}
```

Each API needs:
- Unique `invokerPackage` in config
- Unique output directory
- PSR-4 autoload mapping in composer.json
- Entry in the `files` section for its `DefaultApiInterface.php`

### Custom Template Directory

Override template directory location:

```bash
make generate \
  SPEC_FILE=... \
  CONFIG_FILE=... \
  OUTPUT_DIR=... \
  TEMPLATES_DIR=/path/to/custom/templates
```

## Generated Code Examples

### Controller Method
```php
abstract public function createGame(
    CreateGameRequest $request
): CreateGameResponseInterface;
```

### Handler Interface
```php
interface CreateGameHandlerInterface
{
    public function createGame(string $mode): CreateGameResponseInterface;
}
```

### Response Class
```php
class CreateGame200Response implements CreateGameResponseInterface
{
    public function __construct(private array $data) {}

    public function toJsonResponse(): JsonResponse
    {
        return response()->json($this->data, 200);
    }
}
```

### Route Definition
```php
Route::post('/games', [MyApiController::class, 'createGame'])
    ->middleware($router->hasMiddlewareGroup('api.middlewareGroup.createGame')
        ? 'api.middlewareGroup.createGame'
        : []);
```

## License

This generator is part of the OpenAPI Generator project and follows its license terms.

## Support

For issues, questions, or contributions related to these templates, please refer to the main project repository.
