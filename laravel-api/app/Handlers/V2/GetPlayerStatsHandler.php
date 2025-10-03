<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Scaffolding\Api\GetPlayerStatsHandlerInterface;
use TicTacToeApiV2\Scaffolding\Api\GetPlayerStatsResponseInterface;
use TicTacToeApiV2\Scaffolding\Api\GetPlayerStats200Response;
use TicTacToeApiV2\Scaffolding\Models\PlayerStats;
use TicTacToeApiV2\Scaffolding\Models\Player;

/**
 * Handler for getPlayerStats operation
 * Retrieves player statistics
 */
class GetPlayerStatsHandler implements GetPlayerStatsHandlerInterface
{
    public function handle(string $playerId): GetPlayerStatsResponseInterface
    {
        // Simple implementation - return mock stats
        $player = new Player(
            id: $playerId,
            username: 'player123'
        );

        $stats = new PlayerStats(
            playerId: $playerId,
            gamesPlayed: 10,
            wins: 5,
            losses: 3,
            draws: 2,
            winRate: 0.5,
            currentStreak: 2,
            longestWinStreak: 5,
            player: $player
        );

        return new GetPlayerStats200Response($stats);
    }
}
