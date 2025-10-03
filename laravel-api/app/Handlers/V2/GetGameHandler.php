<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Scaffolding\Api\GetGameHandlerInterface;
use TicTacToeApiV2\Scaffolding\Api\GetGameResponseInterface;
use TicTacToeApiV2\Scaffolding\Api\GetGame200Response;
use TicTacToeApiV2\Scaffolding\Models\Game;
use TicTacToeApiV2\Scaffolding\Models\GameStatus;
use TicTacToeApiV2\Scaffolding\Models\GameMode;

/**
 * Handler for getGame operation
 * Retrieves game details
 */
class GetGameHandler implements GetGameHandlerInterface
{
    public function handle(string $gameId): GetGameResponseInterface
    {
        // Simple implementation - return mock game
        $game = new Game(
            id: $gameId,
            status: GameStatus::IN_PROGRESS,
            mode: GameMode::PVP,
            board: [
                ['X', 'O', '.'],
                ['.', 'X', '.'],
                ['.', '.', 'O']
            ],
            createdAt: new \DateTime('2024-01-01 10:00:00')
        );

        return new GetGame200Response($game);
    }
}
