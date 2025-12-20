<?php declare(strict_types=1);

namespace TicTacToeApiV2\Server\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use TicTacToeApiV2\Server\Api\GetLeaderboardApiInterface;
use TicTacToeApiV2\Server\Http\Responses\GetLeaderboardResponseInterface;
use Crell\Serde\SerdeCommon;

/**
 * GetLeaderboardApiInterface Controller
 *
 * Generated server from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class GetLeaderboardController extends Controller
{
    /**
     * Get leaderboard
     *
     * Retrieves the global leaderboard with top players.
     *
     * Query parameters validation (from OpenAPI spec):
     * - timeframe: \TicTacToeApiV2\Server\Models\GetLeaderboardTimeframeParameter
     * - limit: int, min: 1, max: 100
     *
     * @param GetLeaderboardApiInterface $handler Injected business logic handler
     * @param Request $request
     * @return JsonResponse
     */
    public function getLeaderboard(
        GetLeaderboardApiInterface $handler,
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

}
