<?php declare(strict_types=1);

namespace App\Api\TicTacToe;

use TicTacToeApiV2\Server\Api\GetBoardApiInterface;
use TicTacToeApiV2\Server\Http\Responses\GetBoardApiInterfaceResponseInterface;
use TicTacToeApiV2\Server\Http\Responses\GetBoardApiInterfaceResponseFactory;
use TicTacToeApiV2\Server\Models\Status;
use TicTacToeApiV2\Server\Models\Winner;
use TicTacToeApiV2\Server\Models\NotFoundError;
use TicTacToeApiV2\Server\Models\Mark;
use TicTacToeApiV2\Server\Models\NotFoundErrorAllOfErrorType;

/**
 * API for getBoard operation
 * Retrieves game board state
 */
class GetBoardApi implements GetBoardApiInterface
{
    public function handle(string $gameId): GetBoardApiInterfaceResponseInterface
    {
        // Example: Return 404 NotFound if game doesn't exist
        if ($gameId === '00000000-0000-0000-0000-000000000000') {
            return GetBoardApiInterfaceResponseFactory::status404(
                new NotFoundError(
                    code: 'GAME_NOT_FOUND',
                    message: 'Game not found with the provided ID',
                    details: [],
                    errorType: NotFoundErrorAllOfErrorType::NOT_FOUND
                )
            );
        }

        // Success case - return board with some moves
        $board = [
            [Mark::X, Mark::O, Mark::PERIOD],
            [Mark::PERIOD, Mark::X, Mark::O],
            [Mark::PERIOD, Mark::PERIOD, Mark::X]
        ];

        $status = new Status(
            winner: Winner::X,  // X won with diagonal
            board: $board
        );

        return GetBoardApiInterfaceResponseFactory::status200($status);
    }
}
