<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Server\Api\CreateGameHandlerInterface;
use TicTacToeApiV2\Server\Api\CreateGameResponseInterface;
use TicTacToeApiV2\Server\Api\CreateGame201Response;
use TicTacToeApiV2\Server\Models\CreateGameRequest;
use TicTacToeApiV2\Server\Models\Game;
use TicTacToeApiV2\Server\Models\GameStatus;

/**
 * Handler for createGame operation
 * Creates a new TicTacToe game
 */
class CreateGameHandler implements CreateGameHandlerInterface
{
    public function handle(CreateGameRequest $createGameRequest): CreateGameResponseInterface
    {
        // Example: Return 422 ValidationError if PvP mode without opponentId
        if (isset($createGameRequest->mode)
            && $createGameRequest->mode === \TicTacToeApiV2\Server\Models\GameMode::PVP
            && empty($createGameRequest->opponentId)) {
            return new \TicTacToeApiV2\Server\Api\CreateGame422Response(
                new \TicTacToeApiV2\Server\Models\ValidationError(
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
        // return new \TicTacToeApiV2\Server\Api\CreateGame400Response(
        //     new \TicTacToeApiV2\Server\Models\Error(
        //         code: 'INVALID_REQUEST',
        //         message: 'Invalid game creation request'
        //     )
        // );

        // Example: Return 401 Unauthorized
        // Uncomment to demonstrate:
        // return new \TicTacToeApiV2\Server\Api\CreateGame401Response(
        //     new \TicTacToeApiV2\Server\Models\Error(
        //         code: 'UNAUTHORIZED',
        //         message: 'Authentication required to create games'
        //     )
        // );

        // Success case - create a game with empty board
        $playerX = new \TicTacToeApiV2\Server\Models\Player(
            id: 'player-x-' . uniqid(),
            username: 'PlayerX',
            displayName: 'Player X',
            avatarUrl: 'https://example.com/avatar-x.png'
        );

        $playerO = new \TicTacToeApiV2\Server\Models\Player(
            id: 'player-o-' . uniqid(),
            username: 'PlayerO',
            displayName: 'Player O',
            avatarUrl: 'https://example.com/avatar-o.png'
        );

        $game = new Game(
            id: '550e8400-e29b-41d4-a716-' . uniqid(),
            status: GameStatus::PENDING,
            mode: $createGameRequest->mode ?? \TicTacToeApiV2\Server\Models\GameMode::PVP,
            playerX: $playerX,
            playerO: $playerO,
            currentTurn: \TicTacToeApiV2\Server\Models\Mark::X,
            winner: \TicTacToeApiV2\Server\Models\Winner::PERIOD,
            board: [
                ['.', '.', '.'],
                ['.', '.', '.'],
                ['.', '.', '.']
            ],
            createdAt: new \DateTime(),
            updatedAt: new \DateTime(),
            completedAt: new \DateTime('2099-01-01')  // Far future for pending games
        );

        return new CreateGame201Response($game);
    }
}
