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
        // TicTacToe V2 API Bindings (PSR-4 compliant, per-operation interfaces)
        // ============================================================
        $this->app->bind(\TicTacToeApiV2\Server\Api\CreateGameApiInterface::class, \App\Api\V2\CreateGameApi::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\DeleteGameApiInterface::class, \App\Api\V2\DeleteGameApi::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\GetBoardApiInterface::class, \App\Api\V2\GetBoardApi::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\GetGameApiInterface::class, \App\Api\V2\GetGameApi::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\GetLeaderboardApiInterface::class, \App\Api\V2\GetLeaderboardApi::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\GetMovesApiInterface::class, \App\Api\V2\GetMovesApi::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\GetPlayerStatsApiInterface::class, \App\Api\V2\GetPlayerStatsApi::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\GetSquareApiInterface::class, \App\Api\V2\GetSquareApi::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\ListGamesApiInterface::class, \App\Api\V2\ListGamesApi::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\PutSquareApiInterface::class, \App\Api\V2\PutSquareApi::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
