<?php

namespace Tests\Feature;

use Tests\TestCase;

class HandlerBindingTest extends TestCase
{
    /**
     * Test that PetStore handler interfaces are properly bound in DI container
     */
    public function test_petstore_handlers_are_bound(): void
    {
        // Verify AdminApiHandler binding
        $handler = app(\PetStoreApiV2\Server\Api\AdminApiInterface::class);
        $this->assertInstanceOf(\App\Handlers\PetStore\AdminApiHandler::class, $handler);

        // Verify AnalyticsApiHandler binding
        $handler = app(\PetStoreApiV2\Server\Api\AnalyticsApiInterface::class);
        $this->assertInstanceOf(\App\Handlers\PetStore\AnalyticsApiHandler::class, $handler);

        // Verify CreationApiHandler binding
        $handler = app(\PetStoreApiV2\Server\Api\CreationApiInterface::class);
        $this->assertInstanceOf(\App\Handlers\PetStore\CreationApiHandler::class, $handler);

        // Verify DetailsApiHandler binding
        $handler = app(\PetStoreApiV2\Server\Api\DetailsApiInterface::class);
        $this->assertInstanceOf(\App\Handlers\PetStore\DetailsApiHandler::class, $handler);
    }

    /**
     * Test that TicTacToe handler interfaces are properly bound in DI container
     */
    public function test_tictactoe_handlers_are_bound(): void
    {
        // Verify GameManagementApiHandler binding
        $handler = app(\TicTacToeApiV2\Server\Api\GameManagementApiInterface::class);
        $this->assertInstanceOf(\App\Handlers\V2\GameManagementApiHandler::class, $handler);

        // Verify GameplayApiHandler binding
        $handler = app(\TicTacToeApiV2\Server\Api\GameplayApiInterface::class);
        $this->assertInstanceOf(\App\Handlers\V2\GameplayApiHandler::class, $handler);

        // Verify StatisticsApiHandler binding
        $handler = app(\TicTacToeApiV2\Server\Api\StatisticsApiInterface::class);
        $this->assertInstanceOf(\App\Handlers\V2\StatisticsApiHandler::class, $handler);

        // Verify TicTacApiHandler binding
        $handler = app(\TicTacToeApiV2\Server\Api\TicTacApiInterface::class);
        $this->assertInstanceOf(\App\Handlers\V2\TicTacApiHandler::class, $handler);
    }
}
