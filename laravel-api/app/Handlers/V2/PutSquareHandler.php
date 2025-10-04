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
        // Demo: Start with a board that has some existing moves
        $board = [
            ['.', '.', '.'],
            ['.', '.', '.'],
            ['.', '.', '.']
        ];

        // Place the mark (row and column are already 0-indexed from URL)
        $mark = $moveRequest->mark->value;
        $board[$row][$column] = $mark;

        // Demo: If placing on row 0, fill the rest of the row to show winner detection
        if ($row === 0 && $mark === 'X') {
            $board[0][0] = 'X';
            $board[0][1] = 'X';
            $board[0][2] = 'X';
        }

        // Check for winner (check rows, columns, and diagonals)
        $winner = Winner::PERIOD;

        // Check rows
        for ($i = 0; $i < 3; $i++) {
            if ($board[$i][0] !== '.' && $board[$i][0] === $board[$i][1] && $board[$i][1] === $board[$i][2]) {
                $winner = $board[$i][0] === 'X' ? Winner::X : Winner::O;
            }
        }

        // Check columns
        for ($i = 0; $i < 3; $i++) {
            if ($board[0][$i] !== '.' && $board[0][$i] === $board[1][$i] && $board[1][$i] === $board[2][$i]) {
                $winner = $board[0][$i] === 'X' ? Winner::X : Winner::O;
            }
        }

        // Check diagonals
        if ($board[0][0] !== '.' && $board[0][0] === $board[1][1] && $board[1][1] === $board[2][2]) {
            $winner = $board[0][0] === 'X' ? Winner::X : Winner::O;
        }
        if ($board[0][2] !== '.' && $board[0][2] === $board[1][1] && $board[1][1] === $board[2][0]) {
            $winner = $board[0][2] === 'X' ? Winner::X : Winner::O;
        }

        $status = new Status(
            winner: $winner,
            board: $board
        );

        return new PutSquare200Response($status);
    }
}
