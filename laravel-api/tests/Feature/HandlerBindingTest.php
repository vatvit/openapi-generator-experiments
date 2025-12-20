<?php

namespace Tests\Feature;

use Tests\TestCase;

class HandlerBindingTest extends TestCase
{
    /**
     * Test that PetStore API interfaces are properly bound in DI container
     */
    public function test_petstore_api_bindings_are_bound(): void
    {
        // Verify AddPetApi binding
        $api = app(\PetStoreApiV2\Server\Api\AddPetApiInterface::class);
        $this->assertInstanceOf(\App\Api\PetStore\AddPetApi::class, $api);

        // Verify DeletePetApi binding
        $api = app(\PetStoreApiV2\Server\Api\DeletePetApiInterface::class);
        $this->assertInstanceOf(\App\Api\PetStore\DeletePetApi::class, $api);

        // Verify FindPetByIdApi binding
        $api = app(\PetStoreApiV2\Server\Api\FindPetByIdApiInterface::class);
        $this->assertInstanceOf(\App\Api\PetStore\FindPetByIdApi::class, $api);

        // Verify FindPetsApi binding
        $api = app(\PetStoreApiV2\Server\Api\FindPetsApiInterface::class);
        $this->assertInstanceOf(\App\Api\PetStore\FindPetsApi::class, $api);
    }

    /**
     * Test that TicTacToe API interfaces are properly bound in DI container
     */
    public function test_tictactoe_api_bindings_are_bound(): void
    {
        // Verify CreateGameApi binding
        $api = app(\TicTacToeApiV2\Server\Api\CreateGameApiInterface::class);
        $this->assertInstanceOf(\App\Api\TicTacToe\CreateGameApi::class, $api);

        // Verify DeleteGameApi binding
        $api = app(\TicTacToeApiV2\Server\Api\DeleteGameApiInterface::class);
        $this->assertInstanceOf(\App\Api\TicTacToe\DeleteGameApi::class, $api);

        // Verify GetBoardApi binding
        $api = app(\TicTacToeApiV2\Server\Api\GetBoardApiInterface::class);
        $this->assertInstanceOf(\App\Api\TicTacToe\GetBoardApi::class, $api);

        // Verify GetGameApi binding
        $api = app(\TicTacToeApiV2\Server\Api\GetGameApiInterface::class);
        $this->assertInstanceOf(\App\Api\TicTacToe\GetGameApi::class, $api);

        // Verify GetLeaderboardApi binding
        $api = app(\TicTacToeApiV2\Server\Api\GetLeaderboardApiInterface::class);
        $this->assertInstanceOf(\App\Api\TicTacToe\GetLeaderboardApi::class, $api);

        // Verify GetMovesApi binding
        $api = app(\TicTacToeApiV2\Server\Api\GetMovesApiInterface::class);
        $this->assertInstanceOf(\App\Api\TicTacToe\GetMovesApi::class, $api);

        // Verify GetPlayerStatsApi binding
        $api = app(\TicTacToeApiV2\Server\Api\GetPlayerStatsApiInterface::class);
        $this->assertInstanceOf(\App\Api\TicTacToe\GetPlayerStatsApi::class, $api);

        // Verify GetSquareApi binding
        $api = app(\TicTacToeApiV2\Server\Api\GetSquareApiInterface::class);
        $this->assertInstanceOf(\App\Api\TicTacToe\GetSquareApi::class, $api);

        // Verify ListGamesApi binding
        $api = app(\TicTacToeApiV2\Server\Api\ListGamesApiInterface::class);
        $this->assertInstanceOf(\App\Api\TicTacToe\ListGamesApi::class, $api);

        // Verify PutSquareApi binding
        $api = app(\TicTacToeApiV2\Server\Api\PutSquareApiInterface::class);
        $this->assertInstanceOf(\App\Api\TicTacToe\PutSquareApi::class, $api);
    }
}
