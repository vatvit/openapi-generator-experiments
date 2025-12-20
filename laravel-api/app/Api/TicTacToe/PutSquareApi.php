<?php declare(strict_types=1);

namespace App\Api\TicTacToe;

use TicTacToeApiV2\Server\Api\PutSquareApiInterface;
use TicTacToeApiV2\Server\Http\Responses\PutSquareApiInterfaceResponseInterface;
use TicTacToeApiV2\Server\Http\Responses\PutSquareApiInterfaceResponseFactory;
use TicTacToeApiV2\Server\Models\MoveRequest;
use TicTacToeApiV2\Server\Models\Status;
use TicTacToeApiV2\Server\Models\Winner;
use TicTacToeApiV2\Server\Models\NotFoundError;
use TicTacToeApiV2\Server\Models\NotFoundErrorAllOfErrorType;

/**
 * API for putSquare operation
 * Places a mark on the board
 */
class PutSquareApi implements PutSquareApiInterface
{
    public function handle(
        string $gameId,
        int $row,
        int $column,
        MoveRequest $moveRequest
    ): PutSquareApiInterfaceResponseInterface
    {
        // Example: Return 404 NotFound if game doesn't exist
        if ($gameId === '00000000-0000-0000-0000-000000000000') {
            return PutSquareApiInterfaceResponseFactory::status404(
                new NotFoundError(
                    code: 'GAME_NOT_FOUND',
                    message: 'Game not found with the provided ID',
                    details: [],
                    errorType: NotFoundErrorAllOfErrorType::NOT_FOUND
                )
            );
        }

        // Example: Return 409 Conflict if square already occupied
        if ($row === 2 && $column === 2) {
            return PutSquareApiInterfaceResponseFactory::status409(
                new \TicTacToeApiV2\Server\Models\Error(
                    code: 'SQUARE_OCCUPIED',
                    message: 'Square is already occupied. Please choose another square.'
                )
            );
        }

        // Example: Return 409 Conflict if game is already finished
        if ($gameId === 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa') {
            return PutSquareApiInterfaceResponseFactory::status409(
                new \TicTacToeApiV2\Server\Models\Error(
                    code: 'GAME_FINISHED',
                    message: 'Game is already finished. Cannot make more moves.'
                )
            );
        }

        // Success case - return board with the mark placed
        $board = [
            ['.', '.', '.'],
            ['.', '.', '.'],
            ['.', '.', '.']
        ];

        // Place the mark
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

        return PutSquareApiInterfaceResponseFactory::status200($status);
    }
}
