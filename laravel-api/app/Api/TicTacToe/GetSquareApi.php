<?php declare(strict_types=1);

namespace App\Api\TicTacToe;

use TicTacToeApiV2\Server\Api\GetSquareApiInterface;
use TicTacToeApiV2\Server\Http\Responses\GetSquareApiInterfaceResponseInterface;
use TicTacToeApiV2\Server\Http\Responses\GetSquareApiInterfaceResponseFactory;
use TicTacToeApiV2\Server\Models\SquareResponse;
use TicTacToeApiV2\Server\Models\Mark;
use TicTacToeApiV2\Server\Models\NotFoundError;
use TicTacToeApiV2\Server\Models\NotFoundErrorAllOfErrorType;

/**
 * API for getSquare operation
 * Retrieves a single board square
 */
class GetSquareApi implements GetSquareApiInterface
{
    public function handle(string $gameId, int $row, int $column): GetSquareApiInterfaceResponseInterface
    {
        // Example: Return 404 NotFound if game doesn't exist
        if ($gameId === '00000000-0000-0000-0000-000000000000') {
            return GetSquareApiInterfaceResponseFactory::status404(
                new NotFoundError(
                    code: 'GAME_NOT_FOUND',
                    message: 'Game not found with the provided ID',
                    details: [],
                    errorType: NotFoundErrorAllOfErrorType::NOT_FOUND
                )
            );
        }

        // Success case - return square with a mark
        $mark = ($row === 1 && $column === 1) ? Mark::X : Mark::PERIOD;

        $squareResponse = new SquareResponse(
            row: $row,
            column: $column,
            mark: $mark
        );

        return GetSquareApiInterfaceResponseFactory::status200($squareResponse);
    }
}
