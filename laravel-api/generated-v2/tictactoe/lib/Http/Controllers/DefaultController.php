<?php declare(strict_types=1);

namespace TicTacToeApiV2\Server\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use TicTacToeApiV2\Server\Api\CreateGameHandlerInterface;
use TicTacToeApiV2\Server\Api\CreateGameResponseInterface;
use TicTacToeApiV2\Server\Api\DeleteGameHandlerInterface;
use TicTacToeApiV2\Server\Api\DeleteGameResponseInterface;
use TicTacToeApiV2\Server\Api\GetBoardHandlerInterface;
use TicTacToeApiV2\Server\Api\GetBoardResponseInterface;
use TicTacToeApiV2\Server\Api\GetGameHandlerInterface;
use TicTacToeApiV2\Server\Api\GetGameResponseInterface;
use TicTacToeApiV2\Server\Api\GetLeaderboardHandlerInterface;
use TicTacToeApiV2\Server\Api\GetLeaderboardResponseInterface;
use TicTacToeApiV2\Server\Api\GetMovesHandlerInterface;
use TicTacToeApiV2\Server\Api\GetMovesResponseInterface;
use TicTacToeApiV2\Server\Api\GetPlayerStatsHandlerInterface;
use TicTacToeApiV2\Server\Api\GetPlayerStatsResponseInterface;
use TicTacToeApiV2\Server\Api\GetSquareHandlerInterface;
use TicTacToeApiV2\Server\Api\GetSquareResponseInterface;
use TicTacToeApiV2\Server\Api\ListGamesHandlerInterface;
use TicTacToeApiV2\Server\Api\ListGamesResponseInterface;
use TicTacToeApiV2\Server\Api\PutSquareHandlerInterface;
use TicTacToeApiV2\Server\Api\PutSquareResponseInterface;
use \TicTacToeApiV2\Server\Models\CreateGameRequest;
use \TicTacToeApiV2\Server\Models\MoveRequest;
use Crell\Serde\SerdeCommon;

/**
 * DefaultApiInterface Controller
 *
 * Generated server from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class DefaultController extends Controller
{
    /**
     * Create a new game
     *
     * Creates a new TicTacToe game with specified configuration.
     *
     * Request body validation (from OpenAPI spec):
     * - createGameRequest: required, \TicTacToeApiV2\Server\Models\CreateGameRequest
     *
     * @param CreateGameHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @return JsonResponse
     */
    public function createGame(
        CreateGameHandlerInterface $handler,
        Request $request
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->createGameValidationRules());

        // Extract validated parameters
        // Deserialize request body to \TicTacToeApiV2\Server\Models\CreateGameRequest model
        $serde = new SerdeCommon();
        $createGameRequest = $serde->deserialize($request->getContent(), from: 'json', to: \TicTacToeApiV2\Server\Models\CreateGameRequest::class);

        // Call handler with validated parameters
        $response = $handler->handle(
            $createGameRequest
        );

        // Convert response model to JSON (enforced by interface)
        return $response->toJsonResponse();
    }

    /**
     * Delete a game
     *
     * Deletes a game. Only allowed for game creators or admins.
     *
     * Path parameters:
     * - gameId: string
     *
     * @param DeleteGameHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $gameId
     * @return JsonResponse
     */
    public function deleteGame(
        DeleteGameHandlerInterface $handler,
        Request $request,
        string $gameId
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->deleteGameValidationRules($gameId));

        // Extract validated parameters

        // Call handler with validated parameters
        $response = $handler->handle(
            $gameId
        );

        // Convert response model to JSON (enforced by interface)
        return $response->toJsonResponse();
    }

    /**
     * Get the game board
     *
     * Retrieves the current state of the board and the winner.
     *
     * Path parameters:
     * - gameId: string
     *
     * @param GetBoardHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $gameId
     * @return JsonResponse
     */
    public function getBoard(
        GetBoardHandlerInterface $handler,
        Request $request,
        string $gameId
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->getBoardValidationRules($gameId));

        // Extract validated parameters

        // Call handler with validated parameters
        $response = $handler->handle(
            $gameId
        );

        // Convert response model to JSON (enforced by interface)
        return $response->toJsonResponse();
    }

    /**
     * Get game details
     *
     * Retrieves detailed information about a specific game.
     *
     * Path parameters:
     * - gameId: string
     *
     * @param GetGameHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $gameId
     * @return JsonResponse
     */
    public function getGame(
        GetGameHandlerInterface $handler,
        Request $request,
        string $gameId
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->getGameValidationRules($gameId));

        // Extract validated parameters

        // Call handler with validated parameters
        $response = $handler->handle(
            $gameId
        );

        // Convert response model to JSON (enforced by interface)
        return $response->toJsonResponse();
    }

    /**
     * Get leaderboard
     *
     * Retrieves the global leaderboard with top players.
     *
     * Query parameters validation (from OpenAPI spec):
     * - timeframe: \TicTacToeApiV2\Server\Models\GetLeaderboardTimeframeParameter
     * - limit: int, min: 1, max: 100
     *
     * @param GetLeaderboardHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @return JsonResponse
     */
    public function getLeaderboard(
        GetLeaderboardHandlerInterface $handler,
        Request $request
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->getLeaderboardValidationRules());

        // Extract validated parameters
        $timeframe = $request->query('timeframe', null);
        $limit = $request->query('limit', 10);
        // Cast to int if present
        if ($limit !== null) {
            $limit = (int) $limit;
        }

        // Call handler with validated parameters
        $response = $handler->handle(
            $timeframe,
            $limit
        );

        // Convert response model to JSON (enforced by interface)
        return $response->toJsonResponse();
    }

    /**
     * Get move history
     *
     * Retrieves the complete move history for a game.
     *
     * Path parameters:
     * - gameId: string
     *
     * @param GetMovesHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $gameId
     * @return JsonResponse
     */
    public function getMoves(
        GetMovesHandlerInterface $handler,
        Request $request,
        string $gameId
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->getMovesValidationRules($gameId));

        // Extract validated parameters

        // Call handler with validated parameters
        $response = $handler->handle(
            $gameId
        );

        // Convert response model to JSON (enforced by interface)
        return $response->toJsonResponse();
    }

    /**
     * Get player statistics
     *
     * Retrieves comprehensive statistics for a player.
     *
     * Path parameters:
     * - playerId: string
     *
     * @param GetPlayerStatsHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $playerId
     * @return JsonResponse
     */
    public function getPlayerStats(
        GetPlayerStatsHandlerInterface $handler,
        Request $request,
        string $playerId
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->getPlayerStatsValidationRules($playerId));

        // Extract validated parameters

        // Call handler with validated parameters
        $response = $handler->handle(
            $playerId
        );

        // Convert response model to JSON (enforced by interface)
        return $response->toJsonResponse();
    }

    /**
     * Get a single board square
     *
     * Retrieves the requested square.
     *
     * Path parameters:
     * - gameId: string
     * - row: int
     * - column: int
     *
     * @param GetSquareHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $gameId
     * @param int $row
     * @param int $column
     * @return JsonResponse
     */
    public function getSquare(
        GetSquareHandlerInterface $handler,
        Request $request,
        string $gameId,
        int $row,
        int $column
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->getSquareValidationRules($gameId, $row, $column));

        // Extract validated parameters

        // Call handler with validated parameters
        $response = $handler->handle(
            $gameId,
            $row,
            $column
        );

        // Convert response model to JSON (enforced by interface)
        return $response->toJsonResponse();
    }

    /**
     * List all games
     *
     * Retrieves a paginated list of games with optional filtering.
     *
     * Query parameters validation (from OpenAPI spec):
     * - page: int, min: 1
     * - limit: int, min: 1, max: 100
     * - status: \TicTacToeApiV2\Server\Models\GameStatus
     * - playerId: string
     *
     * @param ListGamesHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @return JsonResponse
     */
    public function listGames(
        ListGamesHandlerInterface $handler,
        Request $request
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->listGamesValidationRules());

        // Extract validated parameters
        $page = $request->query('page', 1);
        // Cast to int if present
        if ($page !== null) {
            $page = (int) $page;
        }
        $limit = $request->query('limit', 20);
        // Cast to int if present
        if ($limit !== null) {
            $limit = (int) $limit;
        }
        $status = $request->query('status', null);
        $playerId = $request->query('playerId', null);

        // Call handler with validated parameters
        $response = $handler->handle(
            $page,
            $limit,
            $status,
            $playerId
        );

        // Convert response model to JSON (enforced by interface)
        return $response->toJsonResponse();
    }

    /**
     * Set a single board square
     *
     * Places a mark on the board and retrieves the whole board and the winner (if any).
     *
     * Request body validation (from OpenAPI spec):
     * - moveRequest: required, \TicTacToeApiV2\Server\Models\MoveRequest
     *
     * Path parameters:
     * - gameId: string
     * - row: int
     * - column: int
     *
     * @param PutSquareHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $gameId
     * @param int $row
     * @param int $column
     * @return JsonResponse
     */
    public function putSquare(
        PutSquareHandlerInterface $handler,
        Request $request,
        string $gameId,
        int $row,
        int $column
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->putSquareValidationRules($gameId, $row, $column));

        // Extract validated parameters
        // Deserialize request body to \TicTacToeApiV2\Server\Models\MoveRequest model
        $serde = new SerdeCommon();
        $moveRequest = $serde->deserialize($request->getContent(), from: 'json', to: \TicTacToeApiV2\Server\Models\MoveRequest::class);

        // Call handler with validated parameters
        $response = $handler->handle(
            $gameId,
            $row,
            $column,
            $moveRequest
        );

        // Convert response model to JSON (enforced by interface)
        return $response->toJsonResponse();
    }


    /**
     * Get validation rules for createGame request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function createGameValidationRules(): array
    {
        return [
        ];
    }

    /**
     * Get validation rules for deleteGame request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function deleteGameValidationRules(string $gameId): array
    {
        return [
        ];
    }

    /**
     * Get validation rules for getBoard request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function getBoardValidationRules(string $gameId): array
    {
        return [
        ];
    }

    /**
     * Get validation rules for getGame request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function getGameValidationRules(string $gameId): array
    {
        return [
        ];
    }

    /**
     * Get validation rules for getLeaderboard request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function getLeaderboardValidationRules(): array
    {
        return [
            'timeframe' => 'sometimes',
            'limit' => 'sometimes|integer|min:1|max:100',
        ];
    }

    /**
     * Get validation rules for getMoves request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function getMovesValidationRules(string $gameId): array
    {
        return [
        ];
    }

    /**
     * Get validation rules for getPlayerStats request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function getPlayerStatsValidationRules(string $playerId): array
    {
        return [
        ];
    }

    /**
     * Get validation rules for getSquare request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function getSquareValidationRules(string $gameId, int $row, int $column): array
    {
        return [
        ];
    }

    /**
     * Get validation rules for listGames request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function listGamesValidationRules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'limit' => 'sometimes|integer|min:1|max:100',
            'status' => 'sometimes',
            'playerId' => 'sometimes|string',
        ];
    }

    /**
     * Get validation rules for putSquare request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function putSquareValidationRules(string $gameId, int $row, int $column): array
    {
        return [
        ];
    }

}
