<?php declare(strict_types=1);

namespace App\Api\TicTacToe;

use TicTacToeApiV2\Server\Api\GetMovesApiInterface;
use TicTacToeApiV2\Server\Http\Responses\GetMovesApiInterfaceResponseInterface;
use TicTacToeApiV2\Server\Http\Responses\GetMovesApiInterfaceResponseFactory;
use TicTacToeApiV2\Server\Models\MoveHistory;
use TicTacToeApiV2\Server\Models\NotFoundError;
use TicTacToeApiV2\Server\Models\NotFoundErrorAllOfErrorType;

/**
 * API for getMoves operation
 * Retrieves move history for a game
 */
class GetMovesApi implements GetMovesApiInterface
{
    public function handle(string $gameId): GetMovesApiInterfaceResponseInterface
    {
        // Example: Return 404 NotFound if game doesn't exist
        if ($gameId === '00000000-0000-0000-0000-000000000000') {
            return GetMovesApiInterfaceResponseFactory::status404(
                new NotFoundError(
                    code: 'GAME_NOT_FOUND',
                    message: 'Game not found with the provided ID',
                    details: [],
                    errorType: NotFoundErrorAllOfErrorType::NOT_FOUND
                )
            );
        }

        // Success case - return move history with sample moves
        $moves = [
            new \TicTacToeApiV2\Server\Models\Move(
                moveNumber: 1,
                playerId: '11111111-1111-1111-1111-111111111111',
                mark: \TicTacToeApiV2\Server\Models\MoveMark::X,
                row: 1,
                column: 1,
                timestamp: new \DateTime('2024-01-01 10:00:00')
            ),
            new \TicTacToeApiV2\Server\Models\Move(
                moveNumber: 2,
                playerId: '22222222-2222-2222-2222-222222222222',
                mark: \TicTacToeApiV2\Server\Models\MoveMark::O,
                row: 1,
                column: 2,
                timestamp: new \DateTime('2024-01-01 10:00:15')
            ),
            new \TicTacToeApiV2\Server\Models\Move(
                moveNumber: 3,
                playerId: '11111111-1111-1111-1111-111111111111',
                mark: \TicTacToeApiV2\Server\Models\MoveMark::X,
                row: 2,
                column: 2,
                timestamp: new \DateTime('2024-01-01 10:00:30')
            )
        ];

        $moveHistory = new MoveHistory(
            gameId: $gameId,
            moves: $moves
        );

        return GetMovesApiInterfaceResponseFactory::status200($moveHistory);
    }
}
