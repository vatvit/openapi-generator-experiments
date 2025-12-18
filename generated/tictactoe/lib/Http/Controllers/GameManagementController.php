<?php declare(strict_types=1);

namespace TicTacToeApiV2\Server\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use TicTacToeApiV2\Server\Api\CreateGameHandlerInterface;
use TicTacToeApiV2\Server\Api\CreateGameResponseInterface;
use TicTacToeApiV2\Server\Api\DeleteGameHandlerInterface;
use TicTacToeApiV2\Server\Api\DeleteGameResponseInterface;
use TicTacToeApiV2\Server\Api\GetGameHandlerInterface;
use TicTacToeApiV2\Server\Api\GetGameResponseInterface;
use TicTacToeApiV2\Server\Api\ListGamesHandlerInterface;
use TicTacToeApiV2\Server\Api\ListGamesResponseInterface;
use \TicTacToeApiV2\Server\Models\CreateGameRequest;
use Crell\Serde\SerdeCommon;

/**
 * GameManagementApiInterface Controller
 *
 * Generated server from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class GameManagementController extends Controller
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
     * Get validation rules for createGame request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function createGameValidationRules(): array
    {
        return [
            'mode' => 'required',
            'opponentId' => 'sometimes|string',
            'isPrivate' => 'sometimes|boolean',
            'metadata' => 'sometimes',
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

}
