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
        // Verify FindPetsHandler binding
        $handler = app(\PetStoreApiV2\Server\Api\FindPetsHandlerInterface::class);
        $this->assertInstanceOf(\App\Handlers\PetStore\FindPetsHandler::class, $handler);

        // Verify FindPetByIdHandler binding
        $handler = app(\PetStoreApiV2\Server\Api\FindPetByIdHandlerInterface::class);
        $this->assertInstanceOf(\App\Handlers\PetStore\FindPetByIdHandler::class, $handler);

        // Verify AddPetHandler binding
        $handler = app(\PetStoreApiV2\Server\Api\AddPetHandlerInterface::class);
        $this->assertInstanceOf(\App\Handlers\PetStore\AddPetHandler::class, $handler);

        // Verify DeletePetHandler binding
        $handler = app(\PetStoreApiV2\Server\Api\DeletePetHandlerInterface::class);
        $this->assertInstanceOf(\App\Handlers\PetStore\DeletePetHandler::class, $handler);
    }

    /**
     * Test that TicTacToe handler interfaces are properly bound in DI container
     */
    public function test_tictactoe_handlers_are_bound(): void
    {
        // Verify CreateGameHandler binding
        $handler = app(\TicTacToeApiV2\Server\Api\CreateGameHandlerInterface::class);
        $this->assertInstanceOf(\App\Handlers\V2\CreateGameHandler::class, $handler);

        // Verify GetBoardHandler binding
        $handler = app(\TicTacToeApiV2\Server\Api\GetBoardHandlerInterface::class);
        $this->assertInstanceOf(\App\Handlers\V2\GetBoardHandler::class, $handler);

        // Verify ListGamesHandler binding
        $handler = app(\TicTacToeApiV2\Server\Api\ListGamesHandlerInterface::class);
        $this->assertInstanceOf(\App\Handlers\V2\ListGamesHandler::class, $handler);

        // Verify PutSquareHandler binding
        $handler = app(\TicTacToeApiV2\Server\Api\PutSquareHandlerInterface::class);
        $this->assertInstanceOf(\App\Handlers\V2\PutSquareHandler::class, $handler);
    }
}
