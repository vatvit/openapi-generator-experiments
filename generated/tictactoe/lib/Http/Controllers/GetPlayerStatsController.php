<?php declare(strict_types=1);

namespace TicTacToeApiV2\Server\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use TicTacToeApiV2\Server\Api\GetPlayerStatsApiInterface;
use TicTacToeApiV2\Server\Http\Responses\GetPlayerStatsResponseInterface;
use Crell\Serde\SerdeCommon;

/**
 * GetPlayerStatsApiInterface Controller
 *
 * Generated server from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class GetPlayerStatsController extends Controller
{
    /**
     * Get player statistics
     *
     * Retrieves comprehensive statistics for a player.
     *
     * Path parameters:
     * - playerId: string
     *
     * @param GetPlayerStatsApiInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $playerId
     * @return JsonResponse
     */
    public function getPlayerStats(
        GetPlayerStatsApiInterface $handler,
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

}
