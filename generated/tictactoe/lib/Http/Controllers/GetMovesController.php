<?php declare(strict_types=1);

namespace TicTacToeApiV2\Server\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use TicTacToeApiV2\Server\Api\GetMovesApiInterface;
use TicTacToeApiV2\Server\Http\Responses\GetMovesResponseInterface;
use Crell\Serde\SerdeCommon;

/**
 * GetMovesApiInterface Controller
 *
 * Generated server from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class GetMovesController extends Controller
{
    /**
     * Get move history
     *
     * Retrieves the complete move history for a game.
     *
     * Path parameters:
     * - gameId: string
     *
     * @param GetMovesApiInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $gameId
     * @return JsonResponse
     */
    public function getMoves(
        GetMovesApiInterface $handler,
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

}
