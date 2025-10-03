<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Scaffolding\Api\PutSquareHandlerInterface;
use TicTacToeApiV2\Scaffolding\Api\PutSquareResponseInterface;
use TicTacToeApiV2\Scaffolding\Api\PutSquare200Response;
use TicTacToeApiV2\Scaffolding\Models\MoveRequest;
use TicTacToeApiV2\Scaffolding\Models\Status;
use TicTacToeApiV2\Scaffolding\Models\Winner;

/**
 * Handler for putSquare operation
 * Places a mark on the board
 */
class PutSquareHandler implements PutSquareHandlerInterface
{
    public function handle(
        string $gameId,
        int $row,
        int $column,
        MoveRequest $moveRequest
    ): PutSquareResponseInterface
    {
        // Simple implementation - return board with the mark placed
        $board = [
            ['.', '.', '.'],
            ['.', '.', '.'],
            ['.', '.', '.']
        ];

        // Place the mark (adjust for 0-indexed array)
        $board[$row - 1][$column - 1] = $moveRequest->mark->value;

        $status = new Status(
            winner: Winner::PERIOD,
            board: $board
        );

        return new PutSquare200Response($status);
    }
}
