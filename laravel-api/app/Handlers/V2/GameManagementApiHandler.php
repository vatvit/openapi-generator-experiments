<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Server\Api\GameManagementApiInterface;
use TicTacToeApiV2\Server\Models\CreateGameRequest;
use TicTacToeApiV2\Server\Models\Game;
use TicTacToeApiV2\Server\Models\GameStatus;
use TicTacToeApiV2\Server\Models\GameMode;
use TicTacToeApiV2\Server\Models\Player;
use TicTacToeApiV2\Server\Models\Mark;
use TicTacToeApiV2\Server\Models\Winner;
use TicTacToeApiV2\Server\Models\BadRequestError;
use TicTacToeApiV2\Server\Models\UnauthorizedError;
use TicTacToeApiV2\Server\Models\ForbiddenError;
use TicTacToeApiV2\Server\Models\NotFoundError;
use TicTacToeApiV2\Server\Models\ValidationError;
use TicTacToeApiV2\Server\Models\NoContent204;
use TicTacToeApiV2\Server\Models\GameListResponse;
use TicTacToeApiV2\Server\Models\Pagination;

/**
 * Handler for Game Management API operations
 * Implements: createGame, deleteGame, getGame, listGames
 */
class GameManagementApiHandler implements GameManagementApiInterface
{
    public function createGame(CreateGameRequest $createGameRequest): Game | BadRequestError | UnauthorizedError | ValidationError
    {
        // Validate PvP mode requires opponentId
        if (isset($createGameRequest->mode)
            && $createGameRequest->mode === GameMode::PVP
            && empty($createGameRequest->opponentId)) {
            return new ValidationError(
                code: 'VALIDATION_ERROR',
                message: 'Validation failed for one or more fields',
                details: [],
                errors: [
                    [
                        'field' => 'opponentId',
                        'message' => 'Opponent ID is required for PvP mode',
                        'value' => null
                    ]
                ]
            );
        }

        // Success case - create a game with empty board
        $playerX = new Player(
            id: 'player-x-' . uniqid(),
            username: 'PlayerX',
            displayName: 'Player X',
            avatarUrl: 'https://example.com/avatar-x.png'
        );

        $playerO = new Player(
            id: 'player-o-' . uniqid(),
            username: 'PlayerO',
            displayName: 'Player O',
            avatarUrl: 'https://example.com/avatar-o.png'
        );

        return new Game(
            id: '550e8400-e29b-41d4-a716-' . uniqid(),
            status: GameStatus::PENDING,
            mode: $createGameRequest->mode ?? GameMode::PVP,
            playerX: $playerX,
            playerO: $playerO,
            currentTurn: Mark::X,
            winner: Winner::PERIOD,
            board: [
                [Mark::PERIOD, Mark::PERIOD, Mark::PERIOD],
                [Mark::PERIOD, Mark::PERIOD, Mark::PERIOD],
                [Mark::PERIOD, Mark::PERIOD, Mark::PERIOD]
            ],
            createdAt: new \DateTime(),
            updatedAt: new \DateTime(),
            completedAt: new \DateTime('2099-01-01')
        );
    }

    public function deleteGame(string $gameId): NoContent204 | ForbiddenError | NotFoundError
    {
        // Example implementation - always succeeds
        return new NoContent204();
    }

    public function getGame(string $gameId): Game | NotFoundError
    {
        // Mock implementation - return a sample game
        $playerX = new Player(
            id: 'player-x-123',
            username: 'PlayerX',
            displayName: 'Player X',
            avatarUrl: 'https://example.com/avatar-x.png'
        );

        $playerO = new Player(
            id: 'player-o-456',
            username: 'PlayerO',
            displayName: 'Player O',
            avatarUrl: 'https://example.com/avatar-o.png'
        );

        return new Game(
            id: $gameId,
            status: GameStatus::IN_PROGRESS,
            mode: GameMode::PVP,
            playerX: $playerX,
            playerO: $playerO,
            currentTurn: Mark::X,
            winner: Winner::PERIOD,
            board: [
                [Mark::X, Mark::PERIOD, Mark::PERIOD],
                [Mark::PERIOD, Mark::O, Mark::PERIOD],
                [Mark::PERIOD, Mark::PERIOD, Mark::PERIOD]
            ],
            createdAt: new \DateTime('-1 hour'),
            updatedAt: new \DateTime(),
            completedAt: new \DateTime('2099-01-01')
        );
    }

    public function listGames(?int $page, ?int $limit, ?GameStatus $status, ?string $playerId): GameListResponse | BadRequestError | UnauthorizedError
    {
        // Mock implementation - return empty list
        return new GameListResponse(
            games: [],
            pagination: new Pagination(
                page: $page ?? 1,
                limit: $limit ?? 10,
                total: 0,
                hasNext: false,
                hasPrevious: false
            )
        );
    }
}
