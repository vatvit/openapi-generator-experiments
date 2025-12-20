<?php declare(strict_types=1);

namespace App\Api\TicTacToe;

use TicTacToeApiV2\Server\Api\GetGameApiInterface;
use TicTacToeApiV2\Server\Http\Responses\GetGameApiInterfaceResponseInterface;
use TicTacToeApiV2\Server\Http\Responses\GetGameApiInterfaceResponseFactory;
use TicTacToeApiV2\Server\Models\Game;
use TicTacToeApiV2\Server\Models\GameStatus;
use TicTacToeApiV2\Server\Models\GameMode;
use TicTacToeApiV2\Server\Models\NotFoundError;
use TicTacToeApiV2\Server\Models\NotFoundErrorAllOfErrorType;

/**
 * API for getGame operation
 * Retrieves game details
 */
class GetGameApi implements GetGameApiInterface
{
    public function handle(string $gameId): GetGameApiInterfaceResponseInterface
    {
        // Example: Return 404 NotFound if game doesn't exist
        if ($gameId === '00000000-0000-0000-0000-000000000000') {
            return GetGameApiInterfaceResponseFactory::status404(
                new NotFoundError(
                    code: 'GAME_NOT_FOUND',
                    message: 'Game not found with the provided ID',
                    details: [],
                    errorType: NotFoundErrorAllOfErrorType::NOT_FOUND
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

        return GetGameApiInterfaceResponseFactory::status200($game);
    }
}
