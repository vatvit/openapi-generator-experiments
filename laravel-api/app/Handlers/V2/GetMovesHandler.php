<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Scaffolding\Api\GetMovesHandlerInterface;
use TicTacToeApiV2\Scaffolding\Api\GetMovesResponseInterface;
use TicTacToeApiV2\Scaffolding\Api\GetMoves200Response;
use TicTacToeApiV2\Scaffolding\Models\MoveHistory;

/**
 * Handler for getMoves operation
 * Retrieves move history for a game
 */
class GetMovesHandler implements GetMovesHandlerInterface
{
    public function handle(string $gameId): GetMovesResponseInterface
    {
        // Example: Return 404 NotFound if game doesn't exist
        if ($gameId === '00000000-0000-0000-0000-000000000000') {
            return new \TicTacToeApiV2\Scaffolding\Api\GetMoves404Response(
                new \TicTacToeApiV2\Scaffolding\Models\Error(
                    code: 'GAME_NOT_FOUND',
                    message: 'Game not found with the provided ID'
                )
            );
        }

        // Success case - return move history with sample moves
        $moves = [
            new \TicTacToeApiV2\Scaffolding\Models\Move(
                moveNumber: 1,
                playerId: '11111111-1111-1111-1111-111111111111',
                mark: \TicTacToeApiV2\Scaffolding\Models\MoveMark::X,
                row: 1,
                column: 1,
                timestamp: new \DateTime('2024-01-01 10:00:00')
            ),
            new \TicTacToeApiV2\Scaffolding\Models\Move(
                moveNumber: 2,
                playerId: '22222222-2222-2222-2222-222222222222',
                mark: \TicTacToeApiV2\Scaffolding\Models\MoveMark::O,
                row: 1,
                column: 2,
                timestamp: new \DateTime('2024-01-01 10:00:15')
            ),
            new \TicTacToeApiV2\Scaffolding\Models\Move(
                moveNumber: 3,
                playerId: '11111111-1111-1111-1111-111111111111',
                mark: \TicTacToeApiV2\Scaffolding\Models\MoveMark::X,
                row: 2,
                column: 2,
                timestamp: new \DateTime('2024-01-01 10:00:30')
            )
        ];

        $moveHistory = new MoveHistory(
            gameId: $gameId,
            moves: $moves
        );

        return new GetMoves200Response($moveHistory);
    }
}
