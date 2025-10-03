<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Scaffolding\Api\GetMovesHandlerInterface;
use TicTacToeApiV2\Scaffolding\Api\GetMovesResponseInterface;
use TicTacToeApiV2\Scaffolding\Api\GetMoves200Response;
use TicTacToeApiV2\Scaffolding\Models\MoveHistory;

/**
 * Handler for getMoves operation
 * Retrieves move history for a game
 */
class GetMovesHandler implements GetMovesHandlerInterface
{
    public function handle(string $gameId): GetMovesResponseInterface
    {
        // Simple implementation - return empty move history
        $moveHistory = new MoveHistory(
            gameId: $gameId,
            moves: []
        );

        return new GetMoves200Response($moveHistory);
    }
}
