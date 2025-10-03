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
        // Simple implementation - return empty list with pagination
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
