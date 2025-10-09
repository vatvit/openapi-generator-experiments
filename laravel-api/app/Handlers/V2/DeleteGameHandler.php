<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Server\Api\DeleteGameHandlerInterface;
use TicTacToeApiV2\Server\Api\DeleteGameResponseInterface;
use TicTacToeApiV2\Server\Api\DeleteGame204Response;

/**
 * Handler for deleteGame operation
 * Deletes a game
 */
class DeleteGameHandler implements DeleteGameHandlerInterface
{
    public function handle(string $gameId): DeleteGameResponseInterface
    {
        // Example: Return 404 NotFound if game doesn't exist
        if ($gameId === '00000000-0000-0000-0000-000000000000') {
            return new \TicTacToeApiV2\Server\Api\DeleteGame404Response(
                new \TicTacToeApiV2\Server\Models\Error(
                    code: 'GAME_NOT_FOUND',
                    message: 'Game not found with the provided ID'
                )
            );
        }

        // Example: Return 403 Forbidden if user is not the creator
        if ($gameId === 'ffffffff-ffff-ffff-ffff-ffffffffffff') {
            return new \TicTacToeApiV2\Server\Api\DeleteGame403Response(
                new \TicTacToeApiV2\Server\Models\Error(
                    code: 'FORBIDDEN',
                    message: 'You do not have permission to delete this game. Only the creator or admins can delete games.'
                )
            );
        }

        // Success case - return 204 No Content
        // In real app, would delete from database
        return new DeleteGame204Response();
    }
}
