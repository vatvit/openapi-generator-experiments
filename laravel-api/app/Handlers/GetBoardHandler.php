<?php declare(strict_types=1);

namespace App\Handlers;

use TicTacToeApi\Scaffolding\Api\GetBoardHandlerInterface;
use TicTacToeApi\Scaffolding\Api\GetBoardResponseInterface;
use TicTacToeApi\Scaffolding\Api\GetBoard200Response;
use TicTacToeApi\Scaffolding\Models\Status;

/**
 * Handler for getBoard operation
 * Implements business logic for retrieving the game board
 */
class GetBoardHandler implements GetBoardHandlerInterface
{
    public function handle(): GetBoardResponseInterface
    {
        // Business logic implementation - return empty board
        $board = [
            ['.', '.', '.'],
            ['.', '.', '.'],
            ['.', '.', '.']
        ];

        $status = new Status(
            winner: '.',
            board: $board
        );

        return new GetBoard200Response($status);
    }
}
