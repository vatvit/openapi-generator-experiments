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
        // PetStore V2 API Bindings (PSR-4 compliant, default generator)
        // ============================================================
        $this->app->bind(\PetStoreApiV2\Server\Api\AdminApiInterface::class, \App\Handlers\PetStore\AdminApiHandler::class);
        $this->app->bind(\PetStoreApiV2\Server\Api\AnalyticsApiInterface::class, \App\Handlers\PetStore\AnalyticsApiHandler::class);
        $this->app->bind(\PetStoreApiV2\Server\Api\CreationApiInterface::class, \App\Handlers\PetStore\CreationApiHandler::class);
        $this->app->bind(\PetStoreApiV2\Server\Api\DetailsApiInterface::class, \App\Handlers\PetStore\DetailsApiHandler::class);
        $this->app->bind(\PetStoreApiV2\Server\Api\InventoryApiInterface::class, \App\Handlers\PetStore\InventoryApiHandler::class);
        $this->app->bind(\PetStoreApiV2\Server\Api\ManagementApiInterface::class, \App\Handlers\PetStore\ManagementApiHandler::class);
        $this->app->bind(\PetStoreApiV2\Server\Api\PetsApiInterface::class, \App\Handlers\PetStore\PetsApiHandler::class);
        $this->app->bind(\PetStoreApiV2\Server\Api\PublicApiInterface::class, \App\Handlers\PetStore\PublicApiHandler::class);
        $this->app->bind(\PetStoreApiV2\Server\Api\ReportingApiInterface::class, \App\Handlers\PetStore\ReportingApiHandler::class);
        $this->app->bind(\PetStoreApiV2\Server\Api\RetrievalApiInterface::class, \App\Handlers\PetStore\RetrievalApiHandler::class);
        $this->app->bind(\PetStoreApiV2\Server\Api\SearchApiInterface::class, \App\Handlers\PetStore\SearchApiHandler::class);
        $this->app->bind(\PetStoreApiV2\Server\Api\WorkflowApiInterface::class, \App\Handlers\PetStore\WorkflowApiHandler::class);

        // ============================================================
        // TicTacToe V2 API Bindings (PSR-4 compliant, default generator)
        // ============================================================
        $this->app->bind(\TicTacToeApiV2\Server\Api\GameManagementApiInterface::class, \App\Handlers\V2\GameManagementApiHandler::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\GameplayApiInterface::class, \App\Handlers\V2\GameplayApiHandler::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\StatisticsApiInterface::class, \App\Handlers\V2\StatisticsApiHandler::class);
        $this->app->bind(\TicTacToeApiV2\Server\Api\TicTacApiInterface::class, \App\Handlers\V2\TicTacApiHandler::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
