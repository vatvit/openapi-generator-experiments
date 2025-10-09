<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // === PetStore V2 API Setup ===
            require_once base_path('generated-v2/petstore/lib/Api/PetsApiInterface.php');
            require_once base_path('generated-v2/petstore/lib/Http/Controllers/DefaultController.php');

            // Bind PetStore V2 handlers
            app()->bind(\PetStoreApiV2\Server\Api\FindPetsHandlerInterface::class, \App\Handlers\PetStore\FindPetsHandler::class);
            app()->bind(\PetStoreApiV2\Server\Api\FindPetByIdHandlerInterface::class, \App\Handlers\PetStore\FindPetByIdHandler::class);
            app()->bind(\PetStoreApiV2\Server\Api\AddPetHandlerInterface::class, \App\Handlers\PetStore\AddPetHandler::class);
            app()->bind(\PetStoreApiV2\Server\Api\DeletePetHandlerInterface::class, \App\Handlers\PetStore\DeletePetHandler::class);

            // Register PetStore V2 routes
            app()->bind('PetStoreApiController', fn() => app(\PetStoreApiV2\Server\Http\Controllers\DefaultController::class));
            $router = app('router');
            require base_path('generated-v2/petstore/routes.php');

            // === TicTacToe V2 API Setup ===
            require_once base_path('generated-v2/tictactoe/lib/Api/DefaultApiInterface.php');
            require_once base_path('generated-v2/tictactoe/lib/Http/Controllers/DefaultController.php');

            // Bind V2 handlers
            app()->bind(\TicTacToeApiV2\Server\Api\CreateGameHandlerInterface::class, \App\Handlers\V2\CreateGameHandler::class);
            app()->bind(\TicTacToeApiV2\Server\Api\DeleteGameHandlerInterface::class, \App\Handlers\V2\DeleteGameHandler::class);
            app()->bind(\TicTacToeApiV2\Server\Api\GetBoardHandlerInterface::class, \App\Handlers\V2\GetBoardHandler::class);
            app()->bind(\TicTacToeApiV2\Server\Api\GetGameHandlerInterface::class, \App\Handlers\V2\GetGameHandler::class);
            app()->bind(\TicTacToeApiV2\Server\Api\GetLeaderboardHandlerInterface::class, \App\Handlers\V2\GetLeaderboardHandler::class);
            app()->bind(\TicTacToeApiV2\Server\Api\GetMovesHandlerInterface::class, \App\Handlers\V2\GetMovesHandler::class);
            app()->bind(\TicTacToeApiV2\Server\Api\GetPlayerStatsHandlerInterface::class, \App\Handlers\V2\GetPlayerStatsHandler::class);
            app()->bind(\TicTacToeApiV2\Server\Api\GetSquareHandlerInterface::class, \App\Handlers\V2\GetSquareHandler::class);
            app()->bind(\TicTacToeApiV2\Server\Api\ListGamesHandlerInterface::class, \App\Handlers\V2\ListGamesHandler::class);
            app()->bind(\TicTacToeApiV2\Server\Api\PutSquareHandlerInterface::class, \App\Handlers\V2\PutSquareHandler::class);

            // Register V2 routes
            Route::group(['prefix' => 'v2'], function () {
                app()->bind('Tic Tac Toe', fn() => app(\TicTacToeApiV2\Server\Http\Controllers\DefaultController::class));

                $router = app('router');
                require base_path('generated-v2/tictactoe/routes.php');
            });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Exclude CSRF for API routes
        $middleware->validateCsrfTokens(except: [
            '/api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
