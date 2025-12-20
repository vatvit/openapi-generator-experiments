<?php declare(strict_types=1);

namespace TicTacToeApiV2\Server\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use TicTacToeApiV2\Server\Api\GetGameApiInterface;
use TicTacToeApiV2\Server\Http\Responses\GetGameResponseInterface;
use Crell\Serde\SerdeCommon;

/**
 * GetGameApiInterface Controller
 *
 * Generated server from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class GetGameController extends Controller
{
    /**
     * Get game details
     *
     * Retrieves detailed information about a specific game.
     *
     * Path parameters:
     * - gameId: string
     *
     * @param GetGameApiInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $gameId
     * @return JsonResponse
     */
    public function getGame(
        GetGameApiInterface $handler,
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

}
