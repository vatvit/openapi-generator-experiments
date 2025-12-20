<?php declare(strict_types=1);

namespace App\Api\TicTacToe;

use TicTacToeApiV2\Server\Api\ListGamesApiInterface;
use TicTacToeApiV2\Server\Http\Responses\ListGamesApiInterfaceResponseInterface;
use TicTacToeApiV2\Server\Http\Responses\ListGamesApiInterfaceResponseFactory;
use TicTacToeApiV2\Server\Models\GameListResponse;
use TicTacToeApiV2\Server\Models\Pagination;
use TicTacToeApiV2\Server\Models\BadRequestError;
use TicTacToeApiV2\Server\Models\BadRequestErrorAllOfErrorType;

/**
 * API for listGames operation
 * Retrieves paginated list of games
 */
class ListGamesApi implements ListGamesApiInterface
{
    public function handle(
        ?int $page,
        ?int $limit,
        ?\TicTacToeApiV2\Server\Models\GameStatus $status,
        ?string $playerId
    ): ListGamesApiInterfaceResponseInterface
    {
        // Example: Return 400 BadRequest if limit exceeds maximum
        if ($limit !== null && $limit > 100) {
            return ListGamesApiInterfaceResponseFactory::status400(
                new BadRequestError(
                    code: 'INVALID_LIMIT',
                    message: 'Limit parameter cannot exceed 100',
                    details: [],
                    errorType: BadRequestErrorAllOfErrorType::BAD_REQUEST
                )
            );
        }

        // Success case - return paginated list
        $currentPage = $page ?? 1;
        $totalGames = 0; // Would query database in real implementation

        $pagination = new Pagination(
            page: $currentPage,
            limit: $limit ?? 20,
            total: $totalGames,
            hasNext: false,
            hasPrevious: false
        );

        $gameListResponse = new GameListResponse(
            games: [],
            pagination: $pagination
        );

        // Return 200 response with pagination headers
        return ListGamesApiInterfaceResponseFactory::status200($gameListResponse, $totalGames, $currentPage);
    }
}
