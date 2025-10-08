<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

// PetStore API imports
use PetStoreApi\Scaffolding\Api\FindPetsHandlerInterface;
use PetStoreApi\Scaffolding\Api\FindPetByIdHandlerInterface;
use PetStoreApi\Scaffolding\Api\AddPetHandlerInterface;
use PetStoreApi\Scaffolding\Api\DeletePetHandlerInterface;
use PetStoreApi\Scaffolding\Http\Controllers\DefaultController as PetStoreController;

// TicTacToe API imports (multiple tags generate multiple controllers)
use TicTacToeApi\Scaffolding\Api\GetBoardHandlerInterface;
use TicTacToeApi\Scaffolding\Api\GetSquareHandlerInterface;
use TicTacToeApi\Scaffolding\Api\PutSquareHandlerInterface;
use TicTacToeApi\Scaffolding\Http\Controllers\TicTacController as TicTacToeControllerA;
use TicTacToeApi\Scaffolding\Http\Controllers\GameplayController as TicTacToeControllerB;

// PetStore Handler implementations
use App\Handlers\FindPetsHandler;
use App\Handlers\FindPetByIdHandler;
use App\Handlers\AddPetHandler;
use App\Handlers\DeletePetHandler;

// TicTacToe Handler implementations
use App\Handlers\GetBoardHandler;
use App\Handlers\GetSquareHandler;
use App\Handlers\PutSquareHandler;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // === PetStore API Setup ===
            // Load generated interfaces/classes (PSR-4 can't autoload multiple classes per file)
            require_once base_path('generated/petstore/lib/Api/DefaultApiInterface.php');
            require_once base_path('generated/petstore/lib/Http/Controllers/DefaultController.php');

            // Load generated model classes
            require_once base_path('generated/petstore/lib/Models/Pet.php');
            require_once base_path('generated/petstore/lib/Models/NewPet.php');
            require_once base_path('generated/petstore/lib/Models/Error.php');
            require_once base_path('generated/petstore/lib/Models/NoContent204.php');

            // Bind operation handlers - business logic implementations
            app()->bind(FindPetsHandlerInterface::class, FindPetsHandler::class);
            app()->bind(FindPetByIdHandlerInterface::class, FindPetByIdHandler::class);
            app()->bind(AddPetHandlerInterface::class, AddPetHandler::class);
            app()->bind(DeletePetHandlerInterface::class, DeletePetHandler::class);

            // Bind controller name from OpenAPI spec to generated controller
            if (!class_exists('PetStoreApiController')) {
                class_alias(PetStoreController::class, 'PetStoreApiController');
            }

            // Register generated API routes (no prefix needed, paths already include /v2)
            Route::group([], function () {
                $router = app('router');
                require base_path('generated/petstore/routes.php');
            });

            // === TicTacToe API Setup ===
            // Load generated interfaces/classes (one per tag)
            // Note: Operations with multiple tags will be duplicated across controllers
            // Load only ONE ApiInterface file to avoid duplicate response interface definitions
            require_once base_path('generated/tictactoe/lib/Api/GameplayApiInterface.php');
            require_once base_path('generated/tictactoe/lib/Http/Controllers/TicTacController.php');
            require_once base_path('generated/tictactoe/lib/Http/Controllers/GameplayController.php');

            // Load generated model classes
            require_once base_path('generated/tictactoe/lib/Models/Mark.php');
            require_once base_path('generated/tictactoe/lib/Models/Status.php');
            require_once base_path('generated/tictactoe/lib/Models/Winner.php');

            // Bind TicTacToe operation handlers
            app()->bind(GetBoardHandlerInterface::class, GetBoardHandler::class);
            app()->bind(GetSquareHandlerInterface::class, GetSquareHandler::class);
            app()->bind(PutSquareHandlerInterface::class, PutSquareHandler::class);

            // Bind controller name from OpenAPI spec to generated controller
            // Routes use info.title from spec as controller name
            // Using GameplayController as it contains all operations (TicTacController only has getBoard)
            if (!class_exists('Tic Tac Toe')) {
                class_alias(TicTacToeControllerB::class, 'Tic Tac Toe');
            }

            // Register generated API routes with prefix
            Route::group(['prefix' => 'tictactoe'], function () {
                $router = app('router');
                require base_path('generated/tictactoe/routes.php');
            });

            // === TicTacToe V2 API Setup ===
            // Load generated V2 interfaces and controller
            require_once base_path('generated-v2/tictactoe/lib/Api/DefaultApiInterface.php');
            require_once base_path('generated-v2/tictactoe/lib/Http/Controllers/DefaultController.php');

            // Bind V2 operation handlers
            app()->bind(\TicTacToeApiV2\Scaffolding\Api\CreateGameHandlerInterface::class, \App\Handlers\V2\CreateGameHandler::class);
            app()->bind(\TicTacToeApiV2\Scaffolding\Api\DeleteGameHandlerInterface::class, \App\Handlers\V2\DeleteGameHandler::class);
            app()->bind(\TicTacToeApiV2\Scaffolding\Api\GetBoardHandlerInterface::class, \App\Handlers\V2\GetBoardHandler::class);
            app()->bind(\TicTacToeApiV2\Scaffolding\Api\GetGameHandlerInterface::class, \App\Handlers\V2\GetGameHandler::class);
            app()->bind(\TicTacToeApiV2\Scaffolding\Api\GetLeaderboardHandlerInterface::class, \App\Handlers\V2\GetLeaderboardHandler::class);
            app()->bind(\TicTacToeApiV2\Scaffolding\Api\GetMovesHandlerInterface::class, \App\Handlers\V2\GetMovesHandler::class);
            app()->bind(\TicTacToeApiV2\Scaffolding\Api\GetPlayerStatsHandlerInterface::class, \App\Handlers\V2\GetPlayerStatsHandler::class);
            app()->bind(\TicTacToeApiV2\Scaffolding\Api\GetSquareHandlerInterface::class, \App\Handlers\V2\GetSquareHandler::class);
            app()->bind(\TicTacToeApiV2\Scaffolding\Api\ListGamesHandlerInterface::class, \App\Handlers\V2\ListGamesHandler::class);
            app()->bind(\TicTacToeApiV2\Scaffolding\Api\PutSquareHandlerInterface::class, \App\Handlers\V2\PutSquareHandler::class);

            // Note: V2 uses same controller name "Tic Tac Toe" as V1 (from OpenAPI spec info.title)
            // The V2 controller extends the generated DefaultController from V2 scaffolding
            // We don't need a separate class alias since both V1 and V2 have their own route groups

            // Register V2 routes (will use existing "Tic Tac Toe" binding but with V2 controller)
            // Temporarily bind to V2 controller for these routes
            Route::group(['prefix' => 'v2'], function () {
                // Override binding for V2 routes
                app()->bind('Tic Tac Toe', fn() => app(\TicTacToeApiV2\Scaffolding\Http\Controllers\DefaultController::class));

                $router = app('router');
                require base_path('generated-v2/tictactoe/routes.php');
            });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Exclude CSRF for API routes
        $middleware->validateCsrfTokens(except: [
            'v2/*',
            'tictactoe/*',
        ]);

        // Define middleware groups for API operations that need custom middleware
        // Generated routes will automatically use these groups if they exist

        // Example: Add logging to findPets operation
        $middleware->group('api.middlewareGroup.findPets', [
            \App\Http\Middleware\LogRequest::class,
        ]);

        // TicTacToe V2 Security Middleware (from OpenAPI security schemes)
        // These operations require Bearer token authentication
        $middleware->group('api.middlewareGroup.createGame', [
            \App\Http\Middleware\ValidateBearerToken::class,
        ]);

        $middleware->group('api.middlewareGroup.deleteGame', [
            \App\Http\Middleware\ValidateBearerToken::class,
        ]);

        $middleware->group('api.middlewareGroup.getGame', [
            \App\Http\Middleware\ValidateBearerToken::class,
        ]);

        $middleware->group('api.middlewareGroup.listGames', [
            \App\Http\Middleware\ValidateBearerToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
