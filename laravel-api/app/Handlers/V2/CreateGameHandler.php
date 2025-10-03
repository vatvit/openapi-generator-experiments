<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Scaffolding\Api\CreateGameHandlerInterface;
use TicTacToeApiV2\Scaffolding\Api\CreateGameResponseInterface;
use TicTacToeApiV2\Scaffolding\Api\CreateGame201Response;
use TicTacToeApiV2\Scaffolding\Models\CreateGameRequest;
use TicTacToeApiV2\Scaffolding\Models\Game;
use TicTacToeApiV2\Scaffolding\Models\GameStatus;

/**
 * Handler for createGame operation
 * Creates a new TicTacToe game
 */
class CreateGameHandler implements CreateGameHandlerInterface
{
    public function handle(CreateGameRequest $createGameRequest): CreateGameResponseInterface
    {
        // Simple implementation - create a game with empty board
        $game = new Game(
            id: '550e8400-e29b-41d4-a716-446655440000',
            status: GameStatus::PENDING,
            mode: $createGameRequest->mode,
            board: [
                ['.', '.', '.'],
                ['.', '.', '.'],
                ['.', '.', '.']
            ],
            createdAt: new \DateTime()
        );

        return new CreateGame201Response($game);
    }
}
