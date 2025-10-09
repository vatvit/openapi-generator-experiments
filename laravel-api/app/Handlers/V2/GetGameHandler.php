<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Server\Api\GetGameHandlerInterface;
use TicTacToeApiV2\Server\Api\GetGameResponseInterface;
use TicTacToeApiV2\Server\Api\GetGame200Response;
use TicTacToeApiV2\Server\Models\Game;
use TicTacToeApiV2\Server\Models\GameStatus;
use TicTacToeApiV2\Server\Models\GameMode;

/**
 * Handler for getGame operation
 * Retrieves game details
 */
class GetGameHandler implements GetGameHandlerInterface
{
    public function handle(string $gameId): GetGameResponseInterface
    {
        // Example: Return 404 NotFound if game doesn't exist
        if ($gameId === '00000000-0000-0000-0000-000000000000') {
            return new \TicTacToeApiV2\Server\Api\GetGame404Response(
                new \TicTacToeApiV2\Server\Models\Error(
                    code: 'GAME_NOT_FOUND',
                    message: 'Game not found with the provided ID'
                )
            );
        }

        // Success case - return mock game
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
