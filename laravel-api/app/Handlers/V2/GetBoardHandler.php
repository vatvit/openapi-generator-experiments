<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Scaffolding\Api\GetBoardHandlerInterface;
use TicTacToeApiV2\Scaffolding\Api\GetBoardResponseInterface;
use TicTacToeApiV2\Scaffolding\Api\GetBoard200Response;
use TicTacToeApiV2\Scaffolding\Models\Status;
use TicTacToeApiV2\Scaffolding\Models\Winner;

/**
 * Handler for getBoard operation
 * Retrieves game board state
 */
class GetBoardHandler implements GetBoardHandlerInterface
{
    public function handle(string $gameId): GetBoardResponseInterface
    {
        // Example: Return 404 NotFound if game doesn't exist
        if ($gameId === '00000000-0000-0000-0000-000000000000') {
            return new \TicTacToeApiV2\Scaffolding\Api\GetBoard404Response(
                new \TicTacToeApiV2\Scaffolding\Models\Error(
                    code: 'GAME_NOT_FOUND',
                    message: 'Game not found with the provided ID'
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
