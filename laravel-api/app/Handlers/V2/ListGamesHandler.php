<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Scaffolding\Api\ListGamesHandlerInterface;
use TicTacToeApiV2\Scaffolding\Api\ListGamesResponseInterface;
use TicTacToeApiV2\Scaffolding\Api\ListGames200Response;
use TicTacToeApiV2\Scaffolding\Models\GameListResponse;
use TicTacToeApiV2\Scaffolding\Models\Game;
use TicTacToeApiV2\Scaffolding\Models\GameStatus;
use TicTacToeApiV2\Scaffolding\Models\GameMode;
use TicTacToeApiV2\Scaffolding\Models\Pagination;

/**
 * Handler for listGames operation
 * Retrieves paginated list of games
 */
class ListGamesHandler implements ListGamesHandlerInterface
{
    public function handle(
        ?int $page,
        ?int $limit,
        ?\TicTacToeApiV2\Scaffolding\Models\GameStatus $status,
        ?string $playerId
    ): ListGamesResponseInterface
    {
        // Example: Return 400 BadRequest if limit exceeds maximum
        if ($limit !== null && $limit > 100) {
            return new \TicTacToeApiV2\Scaffolding\Api\ListGames400Response(
                new \TicTacToeApiV2\Scaffolding\Models\Error(
                    code: 'INVALID_LIMIT',
                    message: 'Limit parameter cannot exceed 100'
                )
            );
        }

        // Example: Return 401 Unauthorized (would check auth in real implementation)
        // Uncomment to demonstrate:
        // return new \TicTacToeApiV2\Scaffolding\Api\ListGames401Response(
        //     new \TicTacToeApiV2\Scaffolding\Models\Error(
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
