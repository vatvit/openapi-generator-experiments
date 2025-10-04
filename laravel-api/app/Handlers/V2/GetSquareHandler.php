<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Scaffolding\Api\GetSquareHandlerInterface;
use TicTacToeApiV2\Scaffolding\Api\GetSquareResponseInterface;
use TicTacToeApiV2\Scaffolding\Api\GetSquare200Response;
use TicTacToeApiV2\Scaffolding\Models\SquareResponse;
use TicTacToeApiV2\Scaffolding\Models\Mark;

/**
 * Handler for getSquare operation
 * Retrieves a single board square
 */
class GetSquareHandler implements GetSquareHandlerInterface
{
    public function handle(string $gameId, int $row, int $column): GetSquareResponseInterface
    {
        // Example: Return 404 NotFound if game doesn't exist
        if ($gameId === '00000000-0000-0000-0000-000000000000') {
            return new \TicTacToeApiV2\Scaffolding\Api\GetSquare404Response(
                new \TicTacToeApiV2\Scaffolding\Models\Error(
                    code: 'GAME_NOT_FOUND',
                    message: 'Game not found with the provided ID'
                )
            );
        }

        // Example: Return 400 BadRequest for invalid coordinates (though this is validated by OpenAPI)
        // This would be caught by path parameter validation, but showing as example
        // Uncomment to demonstrate:
        // if ($row < 1 || $row > 3 || $column < 1 || $column > 3) {
        //     return new \TicTacToeApiV2\Scaffolding\Api\GetSquare400Response(
        //         new \TicTacToeApiV2\Scaffolding\Models\Error(
        //             code: 'INVALID_COORDINATES',
        //             message: 'Row and column must be between 1 and 3'
        //         )
        //     );
        // }

        // Success case - return square with a mark
        $mark = ($row === 1 && $column === 1) ? Mark::X : Mark::PERIOD;

        $squareResponse = new SquareResponse(
            row: $row,
            column: $column,
            mark: $mark
        );

        return new GetSquare200Response($squareResponse);
    }
}
