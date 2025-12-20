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
        // ============================================================
        // PetStore V2 API Bindings (PSR-4 compliant, per-operation interfaces)
        // ============================================================
        $this->app->bind(\PetStoreApiV2\Server\Api\AddPetApiInterface::class, \App\Api\PetStore\AddPetApi::class);
        $this->app->bind(\PetStoreApiV2\Server\Api\DeletePetApiInterface::class, \App\Api\PetStore\DeletePetApi::class);
        $this->app->bind(\PetStoreApiV2\Server\Api\FindPetByIdApiInterface::class, \App\Api\PetStore\FindPetByIdApi::class);
        $this->app->bind(\PetStoreApiV2\Server\Api\FindPetsApiInterface::class, \App\Api\PetStore\FindPetsApi::class);

        // ============================================================
        // TicTacToe API Bindings (PSR-4 compliant, per-operation interfaces)
        // ============================================================
        $this->app->bind(\TicTacToeApiV2\Server\Api\CreateGameApiInterface::class, \App\Api\TicTacToe\CreateGameApi::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\DeleteGameApiInterface::class, \App\Api\TicTacToe\DeleteGameApi::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\GetBoardApiInterface::class, \App\Api\TicTacToe\GetBoardApi::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\GetGameApiInterface::class, \App\Api\TicTacToe\GetGameApi::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\GetLeaderboardApiInterface::class, \App\Api\TicTacToe\GetLeaderboardApi::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\GetMovesApiInterface::class, \App\Api\TicTacToe\GetMovesApi::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\GetPlayerStatsApiInterface::class, \App\Api\TicTacToe\GetPlayerStatsApi::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\GetSquareApiInterface::class, \App\Api\TicTacToe\GetSquareApi::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\ListGamesApiInterface::class, \App\Api\TicTacToe\ListGamesApi::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\PutSquareApiInterface::class, \App\Api\TicTacToe\PutSquareApi::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
