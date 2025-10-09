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
            \TicTacToeApiV2\Server\Api\CreateGameHandlerInterface::class,
            \App\Handlers\V2\CreateGameHandler::class
        );

        $this->app->bind(
            \TicTacToeApiV2\Server\Api\DeleteGameHandlerInterface::class,
            \App\Handlers\V2\DeleteGameHandler::class
        );

        $this->app->bind(
            \TicTacToeApiV2\Server\Api\GetBoardHandlerInterface::class,
            \App\Handlers\V2\GetBoardHandler::class
        );

        $this->app->bind(
            \TicTacToeApiV2\Server\Api\GetGameHandlerInterface::class,
            \App\Handlers\V2\GetGameHandler::class
        );

        $this->app->bind(
            \TicTacToeApiV2\Server\Api\GetLeaderboardHandlerInterface::class,
            \App\Handlers\V2\GetLeaderboardHandler::class
        );

        $this->app->bind(
            \TicTacToeApiV2\Server\Api\GetMovesHandlerInterface::class,
            \App\Handlers\V2\GetMovesHandler::class
        );

        $this->app->bind(
            \TicTacToeApiV2\Server\Api\GetPlayerStatsHandlerInterface::class,
            \App\Handlers\V2\GetPlayerStatsHandler::class
        );

        $this->app->bind(
            \TicTacToeApiV2\Server\Api\GetSquareHandlerInterface::class,
            \App\Handlers\V2\GetSquareHandler::class
        );

        $this->app->bind(
            \TicTacToeApiV2\Server\Api\ListGamesHandlerInterface::class,
            \App\Handlers\V2\ListGamesHandler::class
        );

        $this->app->bind(
            \TicTacToeApiV2\Server\Api\PutSquareHandlerInterface::class,
            \App\Handlers\V2\PutSquareHandler::class
        );

        // Bind TicTacToe V1 Handler interfaces to concrete implementations
        $this->app->bind(
            \TicTacToeApi\Server\Api\GetBoardHandlerInterface::class,
            \App\Handlers\GetBoardHandler::class
        );

        $this->app->bind(
            \TicTacToeApi\Server\Api\GetSquareHandlerInterface::class,
            \App\Handlers\GetSquareHandler::class
        );

        $this->app->bind(
            \TicTacToeApi\Server\Api\PutSquareHandlerInterface::class,
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
