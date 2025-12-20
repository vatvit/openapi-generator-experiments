<?php declare(strict_types=1);

namespace App\Api\V2;

use TicTacToeApiV2\Server\Api\GetBoardApiInterface;
use TicTacToeApiV2\Server\Http\Responses\GetBoardResponseInterface;
use TicTacToeApiV2\Server\Http\Responses\GetBoard200Response;
use TicTacToeApiV2\Server\Http\Responses\GetBoard404Response;
use TicTacToeApiV2\Server\Models\Status;
use TicTacToeApiV2\Server\Models\Winner;
use TicTacToeApiV2\Server\Models\NotFoundError;

/**
 * API for getBoard operation
 * Retrieves game board state
 */
class GetBoardApi implements GetBoardApiInterface
{
    public function handle(string $gameId): GetBoardResponseInterface
    {
        // Example: Return 404 NotFound if game doesn't exist
        if ($gameId === '00000000-0000-0000-0000-000000000000') {
            return new GetBoard404Response(
                new NotFoundError(
                    code: 'GAME_NOT_FOUND',
                    message: 'Game not found with the provided ID',
                    errorType: 'NOT_FOUND'
                )
            );
        }

        // Success case - return board with some moves
        $board = [
            ['X', 'O', '.'],
            ['.', 'X', 'O'],
            ['.', '.', 'X']
        ];

        $status = new Status(
            winner: Winner::X,  // X won with diagonal
            board: $board
        );

        return new GetBoard200Response($status);
    }
}
