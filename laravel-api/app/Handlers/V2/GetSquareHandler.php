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
        // Simple implementation - return empty square
        $squareResponse = new SquareResponse(
            row: $row,
            column: $column,
            mark: Mark::PERIOD
        );

        return new GetSquare200Response($squareResponse);
    }
}
