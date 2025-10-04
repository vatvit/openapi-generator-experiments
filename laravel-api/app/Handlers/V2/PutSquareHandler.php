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
        // Example: Return 404 NotFound if game doesn't exist
        if ($gameId === '00000000-0000-0000-0000-000000000000') {
            return new \TicTacToeApiV2\Scaffolding\Api\PutSquare404Response(
                new \TicTacToeApiV2\Scaffolding\Models\Error(
                    code: 'GAME_NOT_FOUND',
                    message: 'Game not found with the provided ID'
                )
            );
        }

        // Example: Return 409 Conflict if square already occupied
        if ($row === 2 && $column === 2) {
            return new \TicTacToeApiV2\Scaffolding\Api\PutSquare409Response(
                new \TicTacToeApiV2\Scaffolding\Models\Error(
                    code: 'SQUARE_OCCUPIED',
                    message: 'Square is already occupied. Please choose another square.'
                )
            );
        }

        // Example: Return 409 Conflict if game is already finished
        if ($gameId === 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa') {
            return new \TicTacToeApiV2\Scaffolding\Api\PutSquare409Response(
                new \TicTacToeApiV2\Scaffolding\Models\Error(
                    code: 'GAME_FINISHED',
                    message: 'Game is already finished. Cannot make more moves.'
                )
            );
        }

        // Example: Return 400 BadRequest for invalid input
        // Uncomment to demonstrate:
        // return new \TicTacToeApiV2\Scaffolding\Api\PutSquare400Response(
        //     new \TicTacToeApiV2\Scaffolding\Models\Error(
        //         code: 'INVALID_MOVE',
        //         message: 'Invalid move request'
        //     )
        // );

        // Success case - return board with the mark placed
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
