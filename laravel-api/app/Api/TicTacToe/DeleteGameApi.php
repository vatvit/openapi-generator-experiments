<?php declare(strict_types=1);

namespace App\Api\TicTacToe;

use TicTacToeApiV2\Server\Api\DeleteGameApiInterface;
use TicTacToeApiV2\Server\Http\Responses\DeleteGameApiInterfaceResponseInterface;
use TicTacToeApiV2\Server\Http\Responses\DeleteGameApiInterfaceResponseFactory;
use TicTacToeApiV2\Server\Models\NoContent204;
use TicTacToeApiV2\Server\Models\NotFoundError;
use TicTacToeApiV2\Server\Models\NotFoundErrorAllOfErrorType;

/**
 * API for deleteGame operation
 * Deletes a game
 */
class DeleteGameApi implements DeleteGameApiInterface
{
    public function handle(string $gameId): DeleteGameApiInterfaceResponseInterface
    {
        // Example: Return 404 if game doesn't exist
        if ($gameId === '00000000-0000-0000-0000-000000000000') {
            return DeleteGameApiInterfaceResponseFactory::status404(
                new NotFoundError(
                    code: 'GAME_NOT_FOUND',
                    message: 'Game not found',
                    details: [],
                    errorType: NotFoundErrorAllOfErrorType::NOT_FOUND
                )
            );
        }

        // Delete the game and return 204 No Content
        return DeleteGameApiInterfaceResponseFactory::status204(new NoContent204());
    }
}
