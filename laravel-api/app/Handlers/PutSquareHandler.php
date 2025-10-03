<?php declare(strict_types=1);

namespace App\Handlers;

use TicTacToeApi\Scaffolding\Api\PutSquareHandlerInterface;
use TicTacToeApi\Scaffolding\Api\PutSquareResponseInterface;
use TicTacToeApi\Scaffolding\Api\PutSquare200Response;
use TicTacToeApi\Scaffolding\Models\Status;
use TicTacToeApi\Scaffolding\Models\Winner;

/**
 * Handler for putSquare operation
 * Implements business logic for placing a mark on the board
 */
class PutSquareHandler implements PutSquareHandlerInterface
{
    public function handle(int $row, int $column, string $body): PutSquareResponseInterface
    {
        // Business logic implementation - update board and return status
        $board = [
            ['.', '.', '.'],
            ['.', '.', '.'],
            ['.', '.', '.']
        ];

        // Place the mark (simple implementation - not checking for existing marks)
        $board[$row - 1][$column - 1] = $body;

        $status = new Status(
            winner: Winner::PERIOD,
            board: $board
        );

        return new PutSquare200Response($status);
    }
}
