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
        // Example: Return 422 ValidationError if PvP mode without opponentId
        if ($createGameRequest->mode === \TicTacToeApiV2\Scaffolding\Models\GameMode::PVP
            && empty($createGameRequest->opponentId)) {
            return new \TicTacToeApiV2\Scaffolding\Api\CreateGame422Response(
                new \TicTacToeApiV2\Scaffolding\Models\ValidationError(
                    code: 'VALIDATION_ERROR',
                    message: 'Validation failed for one or more fields',
                    errors: [
                        [
                            'field' => 'opponentId',
                            'message' => 'Opponent ID is required for PvP mode',
                            'value' => null
                        ]
                    ]
                )
            );
        }

        // Example: Return 400 BadRequest for invalid data
        // Uncomment to demonstrate:
        // return new \TicTacToeApiV2\Scaffolding\Api\CreateGame400Response(
        //     new \TicTacToeApiV2\Scaffolding\Models\Error(
        //         code: 'INVALID_REQUEST',
        //         message: 'Invalid game creation request'
        //     )
        // );

        // Example: Return 401 Unauthorized
        // Uncomment to demonstrate:
        // return new \TicTacToeApiV2\Scaffolding\Api\CreateGame401Response(
        //     new \TicTacToeApiV2\Scaffolding\Models\Error(
        //         code: 'UNAUTHORIZED',
        //         message: 'Authentication required to create games'
        //     )
        // );

        // Success case - create a game with empty board
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
