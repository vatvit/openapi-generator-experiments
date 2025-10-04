<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind TicTacToe V2 Handler interfaces to concrete implementations
        $this->app->bind(
            \TicTacToeApiV2\Scaffolding\Api\CreateGameHandlerInterface::class,
            \App\Handlers\V2\CreateGameHandler::class
        );

        $this->app->bind(
            \TicTacToeApiV2\Scaffolding\Api\DeleteGameHandlerInterface::class,
            \App\Handlers\V2\DeleteGameHandler::class
        );

        $this->app->bind(
            \TicTacToeApiV2\Scaffolding\Api\GetBoardHandlerInterface::class,
            \App\Handlers\V2\GetBoardHandler::class
        );

        $this->app->bind(
            \TicTacToeApiV2\Scaffolding\Api\GetGameHandlerInterface::class,
            \App\Handlers\V2\GetGameHandler::class
        );

        $this->app->bind(
            \TicTacToeApiV2\Scaffolding\Api\GetLeaderboardHandlerInterface::class,
            \App\Handlers\V2\GetLeaderboardHandler::class
        );

        $this->app->bind(
            \TicTacToeApiV2\Scaffolding\Api\GetMovesHandlerInterface::class,
            \App\Handlers\V2\GetMovesHandler::class
        );

        $this->app->bind(
            \TicTacToeApiV2\Scaffolding\Api\GetPlayerStatsHandlerInterface::class,
            \App\Handlers\V2\GetPlayerStatsHandler::class
        );

        $this->app->bind(
            \TicTacToeApiV2\Scaffolding\Api\GetSquareHandlerInterface::class,
            \App\Handlers\V2\GetSquareHandler::class
        );

        $this->app->bind(
            \TicTacToeApiV2\Scaffolding\Api\ListGamesHandlerInterface::class,
            \App\Handlers\V2\ListGamesHandler::class
        );

        $this->app->bind(
            \TicTacToeApiV2\Scaffolding\Api\PutSquareHandlerInterface::class,
            \App\Handlers\V2\PutSquareHandler::class
        );

        // Bind TicTacToe V1 Handler interfaces to concrete implementations
        $this->app->bind(
            \TicTacToeApi\Scaffolding\Api\GetBoardHandlerInterface::class,
            \App\Handlers\GetBoardHandler::class
        );

        $this->app->bind(
            \TicTacToeApi\Scaffolding\Api\GetSquareHandlerInterface::class,
            \App\Handlers\GetSquareHandler::class
        );

        $this->app->bind(
            \TicTacToeApi\Scaffolding\Api\PutSquareHandlerInterface::class,
            \App\Handlers\PutSquareHandler::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
