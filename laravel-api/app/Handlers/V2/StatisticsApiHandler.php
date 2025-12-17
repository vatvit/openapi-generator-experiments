<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Server\Api\StatisticsApiInterface;
use TicTacToeApiV2\Server\Models\Leaderboard;
use TicTacToeApiV2\Server\Models\PlayerStats;
use TicTacToeApiV2\Server\Models\GetLeaderboardTimeframeParameter;
use TicTacToeApiV2\Server\Models\NotFoundError;

/**
 * Handler for Statistics API operations
 * Implements: getLeaderboard, getPlayerStats
 */
class StatisticsApiHandler implements StatisticsApiInterface
{
    public function getLeaderboard(?GetLeaderboardTimeframeParameter $timeframe, ?int $limit): Leaderboard
    {
        // Mock implementation - return empty leaderboard
        return new Leaderboard(
            entries: [],
            timeframe: $timeframe?->value ?? 'all-time',
            updatedAt: new \DateTime()
        );
    }

    public function getPlayerStats(string $playerId): PlayerStats | NotFoundError
    {
        // Mock implementation - return sample player stats
        return new PlayerStats(
            playerId: $playerId,
            gamesPlayed: 0,
            gamesWon: 0,
            gamesLost: 0,
            gamesDraw: 0,
            winRate: 0.0
        );
    }
}
