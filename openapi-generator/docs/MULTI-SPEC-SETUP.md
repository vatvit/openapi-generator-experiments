# Multiple OpenAPI Specs Setup

## The Problem

When generating from multiple OpenAPI specs, you'll have collisions if not configured properly.

## Solution: Separate Namespaces & Directories

### Directory Structure

```
specs/                  # OpenAPI specifications
├── petshop-extended.yaml
└── tictactoe.json

laravel-api/generated/
├── petstore/           # From petshop-extended.yaml
│   └── lib/
│       ├── Api/
│       │   └── DefaultApiInterface.php
│       ├── Http/Controllers/
│       │   └── DefaultController.php
│       ├── Models/
│       └── routes.php
│
└── tictactoe/          # From tictactoe.json
    └── lib/
        ├── Api/
        │   └── DefaultApiInterface.php
        ├── Http/Controllers/
        │   └── DefaultController.php
        ├── Models/
        └── routes.php
```

### Config Files

**config/petstore-server-config.json:**
```json
{
  "invokerPackage": "PetStoreApi\\Server",
  "modelPackage": "Models",
  "apiPackage": "Api",
  "appName": "PetStoreApiController"
}
```

**config/tictactoe-server-config.json:**
```json
{
  "invokerPackage": "TicTacToeApi\\Server",
  "modelPackage": "Models",
  "apiPackage": "Api",
  "appName": "TicTacToeApiController"
}
```

### Generation Commands

```bash
# Generate both APIs
make generate-server

# Or generate individually:
make generate-petstore
make generate-tictactoe

# Manual generation:
docker run --rm -v $(pwd):/local openapitools/openapi-generator-cli generate \
  -i /local/specs/petshop-extended.yaml \
  -g php-laravel \
  -o /local/laravel-api/generated/petstore \
  -c /local/config/petstore-server-config.json \
  --template-dir /local/templates/php-laravel-server

docker run --rm -v $(pwd):/local openapitools/openapi-generator-cli generate \
  -i /local/specs/tictactoe.json \
  -g php-laravel \
  -o /local/laravel-api/generated/tictactoe \
  -c /local/config/tictactoe-server-config.json \
  --template-dir /local/templates/php-laravel-server
```

### Bootstrap Configuration

**laravel-api/bootstrap/app.php:**

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

// PetStore API imports
use PetStoreApi\Server\Api\FindPetsHandlerInterface;
use PetStoreApi\Server\Api\FindPetByIdHandlerInterface;
use PetStoreApi\Server\Api\AddPetHandlerInterface;
use PetStoreApi\Server\Api\DeletePetHandlerInterface;
use PetStoreApi\Server\Http\Controllers\DefaultController as PetStoreController;

// TicTacToe API imports
use TicTacToeApi\Server\Api\GetBoardHandlerInterface;
use TicTacToeApi\Server\Api\GetSquareHandlerInterface;
use TicTacToeApi\Server\Api\PutSquareHandlerInterface;
use TicTacToeApi\Server\Http\Controllers\DefaultController as TicTacToeController;

// Handler implementations
use App\Handlers\FindPetsHandler;
use App\Handlers\FindPetByIdHandler;
use App\Handlers\AddPetHandler;
use App\Handlers\DeletePetHandler;
use App\Handlers\GetBoardHandler;
use App\Handlers\GetSquareHandler;
use App\Handlers\PutSquareHandler;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        then: function () {
            // === PetStore API Setup ===
            require_once base_path('generated/petstore/lib/Api/DefaultApiInterface.php');
            require_once base_path('generated/petstore/lib/Http/Controllers/DefaultController.php');

            app()->bind(FindPetsHandlerInterface::class, FindPetsHandler::class);
            app()->bind(FindPetByIdHandlerInterface::class, FindPetByIdHandler::class);
            app()->bind(AddPetHandlerInterface::class, AddPetHandler::class);
            app()->bind(DeletePetHandlerInterface::class, DeletePetHandler::class);

            if (!class_exists('PetStoreApiController')) {
                class_alias(PetStoreController::class, 'PetStoreApiController');
            }

            Route::group([], function () {
                $router = app('router');
                require base_path('generated/petstore/routes.php');
            });

            // === TicTacToe API Setup ===
            require_once base_path('generated/tictactoe/lib/Api/DefaultApiInterface.php');
            require_once base_path('generated/tictactoe/lib/Http/Controllers/DefaultController.php');

            app()->bind(GetBoardHandlerInterface::class, GetBoardHandler::class);
            app()->bind(GetSquareHandlerInterface::class, GetSquareHandler::class);
            app()->bind(PutSquareHandlerInterface::class, PutSquareHandler::class);

            // Routes use info.title from spec as controller name
            if (!class_exists('Tic Tac Toe')) {
                class_alias(TicTacToeController::class, 'Tic Tac Toe');
            }

            Route::group(['prefix' => 'tictactoe'], function () {
                $router = app('router');
                require base_path('generated/tictactoe/routes.php');
            });
        }
    )
    ->create();
```

### Handler Organization

```
laravel-api/app/Handlers/
├── FindPetsHandler.php
├── FindPetByIdHandler.php
├── AddPetHandler.php
├── DeletePetHandler.php
├── GetBoardHandler.php
├── GetSquareHandler.php
└── PutSquareHandler.php
```

## Key Points

1. **Different output directories** (`-o` flag) - Prevents file collisions
2. **Different namespaces** (`invokerPackage`) - Prevents class collisions
   - PetStore: `PetStoreApi\Server`
   - TicTacToe: `TicTacToeApi\Server`
3. **Import aliases** - Use `as` keyword when importing same class names
   - Both generate `DefaultController` but in different namespaces
4. **Class aliases** - Map controller names from specs to actual classes
   - PetStore uses `appName` from config: `'PetStoreApiController'`
   - TicTacToe uses `info.title` from spec: `'Tic Tac Toe'`
5. **Route prefixes** - Each API can have its own URL prefix
   - PetStore: `/v2/pets` (prefix in spec paths)
   - TicTacToe: `/tictactoe/*` (prefix in Route::group)
6. **Avoid using tags** - Keep specs without tags for simpler setup
   - **Recommended**: No tags - generates single `DefaultController` and `DefaultApiInterface`
   - **Not recommended**: With tags - generates separate controllers per tag (e.g., `GameplayController`, `PetsController`)
   - Tags complicate multi-spec setup as each tag creates separate files to manage
   - Current implementation uses `DefaultController` for all specs

## Result

- ✅ No namespace collisions (different `invokerPackage` per spec)
- ✅ No file collisions (different output directories)
- ✅ No class collisions (namespace isolation)
- ✅ Clean separation of concerns
- ✅ Each API independently generated and maintained
- ✅ Both APIs can use `DefaultController` class name without conflict
