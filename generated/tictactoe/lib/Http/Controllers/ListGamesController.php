<?php declare(strict_types=1);

namespace TicTacToeApiV2\Server\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use TicTacToeApiV2\Server\Api\ListGamesApiInterface;
use TicTacToeApiV2\Server\Http\Responses\ListGamesResponseInterface;
use Crell\Serde\SerdeCommon;

/**
 * ListGamesApiInterface Controller
 *
 * Generated server from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class ListGamesController extends Controller
{
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
     * @param ListGamesApiInterface $handler Injected business logic handler
     * @param Request $request
     * @return JsonResponse
     */
    public function listGames(
        ListGamesApiInterface $handler,
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
