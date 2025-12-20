<?php declare(strict_types=1);

namespace App\Api\TicTacToe;

use TicTacToeApiV2\Server\Api\DeleteGameApiInterface;
use TicTacToeApiV2\Server\Http\Responses\DeleteGameResponseInterface;
use TicTacToeApiV2\Server\Http\Responses\DeleteGame204Response;
use TicTacToeApiV2\Server\Http\Responses\DeleteGame404Response;
use TicTacToeApiV2\Server\Models\NoContent204;
use TicTacToeApiV2\Server\Models\NotFoundError;

/**
 * API for deleteGame operation
 * Deletes a game
 */
class DeleteGameApi implements DeleteGameApiInterface
{
    public function handle(string $gameId): DeleteGameResponseInterface
    {
        // Example: Return 404 if game doesn't exist
        if ($gameId === '00000000-0000-0000-0000-000000000000') {
            return new DeleteGame404Response(
                new NotFoundError(
                    code: 'GAME_NOT_FOUND',
                    message: 'Game not found',
                    errorType: 'NOT_FOUND'
                )
            );
        }

        // Delete the game and return 204 No Content
        return new DeleteGame204Response(new NoContent204());
    }
}
