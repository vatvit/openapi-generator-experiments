<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Scaffolding\Api\GetLeaderboardHandlerInterface;
use TicTacToeApiV2\Scaffolding\Api\GetLeaderboardResponseInterface;
use TicTacToeApiV2\Scaffolding\Api\GetLeaderboard200Response;
use TicTacToeApiV2\Scaffolding\Models\Leaderboard;
use TicTacToeApiV2\Scaffolding\Models\LeaderboardEntry;
use TicTacToeApiV2\Scaffolding\Models\Player;

/**
 * Handler for getLeaderboard operation
 * Retrieves global leaderboard
 */
class GetLeaderboardHandler implements GetLeaderboardHandlerInterface
{
    public function handle(
        ?\TicTacToeApiV2\Scaffolding\Models\GetLeaderboardTimeframeParameter $timeframe,
        ?int $limit
    ): GetLeaderboardResponseInterface
    {
        // Simple implementation - return empty leaderboard
        $leaderboard = new Leaderboard(
            timeframe: $timeframe?->value ?? 'all-time',
            entries: [],
            generatedAt: new \DateTime()
        );

        return new GetLeaderboard200Response($leaderboard);
    }
}
