<?php declare(strict_types=1);

namespace App\Api\TicTacToe;

use TicTacToeApiV2\Server\Api\GetPlayerStatsApiInterface;
use TicTacToeApiV2\Server\Http\Responses\GetPlayerStatsResponseInterface;
use TicTacToeApiV2\Server\Http\Responses\GetPlayerStats200Response;
use TicTacToeApiV2\Server\Http\Responses\GetPlayerStats404Response;
use TicTacToeApiV2\Server\Models\PlayerStats;
use TicTacToeApiV2\Server\Models\Player;
use TicTacToeApiV2\Server\Models\NotFoundError;

/**
 * API for getPlayerStats operation
 * Retrieves player statistics
 */
class GetPlayerStatsApi implements GetPlayerStatsApiInterface
{
    public function handle(string $playerId): GetPlayerStatsResponseInterface
    {
        // Example: Return 404 NotFound if player doesn't exist
        if ($playerId === '00000000-0000-0000-0000-000000000000') {
            return new GetPlayerStats404Response(
                new NotFoundError(
                    code: 'PLAYER_NOT_FOUND',
                    message: 'Player not found with the provided ID',
                    errorType: 'NOT_FOUND'
                )
            );
        }

        // Success case - return comprehensive player stats
        $player = new Player(
            id: $playerId,
            username: 'player123',
            displayName: 'Pro Player',
            avatarUrl: 'https://example.com/avatars/player123.png'
        );

        $stats = new PlayerStats(
            playerId: $playerId,
            gamesPlayed: 42,
            wins: 25,
            losses: 12,
            draws: 5,
            winRate: 0.595,
            currentStreak: 3,
            longestWinStreak: 8,
            player: $player
        );

        return new GetPlayerStats200Response($stats);
    }
}
