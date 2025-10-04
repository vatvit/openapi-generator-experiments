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
        // Success case - return leaderboard with top players
        $entries = [
            new LeaderboardEntry(
                rank: 1,
                player: new Player(
                    id: '11111111-1111-1111-1111-111111111111',
                    username: 'champion',
                    displayName: 'The Champion',
                    avatarUrl: 'https://example.com/avatars/champion.png'
                ),
                score: 9500,
                wins: 95,
                gamesPlayed: 120
            ),
            new LeaderboardEntry(
                rank: 2,
                player: new Player(
                    id: '22222222-2222-2222-2222-222222222222',
                    username: 'pro_player',
                    displayName: 'Pro Player',
                    avatarUrl: 'https://example.com/avatars/pro.png'
                ),
                score: 8750,
                wins: 87,
                gamesPlayed: 110
            ),
            new LeaderboardEntry(
                rank: 3,
                player: new Player(
                    id: '33333333-3333-3333-3333-333333333333',
                    username: 'tictac_master',
                    displayName: 'TicTac Master'
                ),
                score: 8200,
                wins: 82,
                gamesPlayed: 105
            )
        ];

        // Limit entries if specified
        if ($limit !== null) {
            $entries = array_slice($entries, 0, $limit);
        }

        $leaderboard = new Leaderboard(
            timeframe: $timeframe?->value ?? 'all-time',
            entries: $entries,
            generatedAt: new \DateTime()
        );

        return new GetLeaderboard200Response($leaderboard);
    }
}
