<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Server\Api\ListGamesHandlerInterface;
use TicTacToeApiV2\Server\Api\ListGamesResponseInterface;
use TicTacToeApiV2\Server\Api\ListGames200Response;
use TicTacToeApiV2\Server\Models\GameListResponse;
use TicTacToeApiV2\Server\Models\Game;
use TicTacToeApiV2\Server\Models\GameStatus;
use TicTacToeApiV2\Server\Models\GameMode;
use TicTacToeApiV2\Server\Models\Pagination;

/**
 * Handler for listGames operation
 * Retrieves paginated list of games
 */
class ListGamesHandler implements ListGamesHandlerInterface
{
    public function handle(
        ?int $page,
        ?int $limit,
        ?\TicTacToeApiV2\Server\Models\GameStatus $status,
        ?string $playerId
    ): ListGamesResponseInterface
    {
        // Example: Return 400 BadRequest if limit exceeds maximum
        if ($limit !== null && $limit > 100) {
            return new \TicTacToeApiV2\Server\Api\ListGames400Response(
                new \TicTacToeApiV2\Server\Models\Error(
                    code: 'INVALID_LIMIT',
                    message: 'Limit parameter cannot exceed 100'
                )
            );
        }

        // Example: Return 401 Unauthorized (would check auth in real implementation)
        // Uncomment to demonstrate:
        // return new \TicTacToeApiV2\Server\Api\ListGames401Response(
        //     new \TicTacToeApiV2\Server\Models\Error(
        //         code: 'UNAUTHORIZED',
        //         message: 'Authentication required'
        //     )
        // );

        // Success case - return paginated list
        $pagination = new Pagination(
            page: $page ?? 1,
            limit: $limit ?? 20,
            total: 0,
            hasNext: false,
            hasPrevious: false
        );

        $gameListResponse = new GameListResponse(
            games: [],
            pagination: $pagination
        );

        return new ListGames200Response($gameListResponse);
    }
}
