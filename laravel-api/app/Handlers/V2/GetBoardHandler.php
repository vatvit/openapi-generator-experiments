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
        // Simple implementation - return empty board
        $board = [
            ['.', '.', '.'],
            ['.', '.', '.'],
            ['.', '.', '.']
        ];

        $status = new Status(
            winner: Winner::PERIOD,
            board: $board
        );

        return new GetBoard200Response($status);
    }
}
