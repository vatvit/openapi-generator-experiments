<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Scaffolding\Api\DeleteGameHandlerInterface;
use TicTacToeApiV2\Scaffolding\Api\DeleteGameResponseInterface;
use TicTacToeApiV2\Scaffolding\Api\DeleteGame204Response;

/**
 * Handler for deleteGame operation
 * Deletes a game
 */
class DeleteGameHandler implements DeleteGameHandlerInterface
{
    public function handle(string $gameId): DeleteGameResponseInterface
    {
        // Simple implementation - just return success
        // In real app, would delete from database

        return new DeleteGame204Response();
    }
}
