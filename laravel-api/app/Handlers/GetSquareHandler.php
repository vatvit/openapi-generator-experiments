<?php declare(strict_types=1);

namespace App\Handlers;

use TicTacToeApi\Scaffolding\Api\GetSquareHandlerInterface;
use TicTacToeApi\Scaffolding\Api\GetSquareResponseInterface;
use TicTacToeApi\Scaffolding\Api\GetSquare200Response;

/**
 * Handler for getSquare operation
 * Implements business logic for retrieving a single board square
 */
class GetSquareHandler implements GetSquareHandlerInterface
{
    public function handle(int $row, int $column): GetSquareResponseInterface
    {
        // Business logic implementation - return empty square
        $mark = '.';

        return new GetSquare200Response($mark);
    }
}
