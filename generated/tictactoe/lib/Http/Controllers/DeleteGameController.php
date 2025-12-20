<?php declare(strict_types=1);

namespace TicTacToeApiV2\Server\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use TicTacToeApiV2\Server\Api\DeleteGameApiInterface;
use TicTacToeApiV2\Server\Http\Responses\DeleteGameResponseInterface;
use Crell\Serde\SerdeCommon;

/**
 * DeleteGameApiInterface Controller
 *
 * Generated server from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class DeleteGameController extends Controller
{
    /**
     * Delete a game
     *
     * Deletes a game. Only allowed for game creators or admins.
     *
     * Path parameters:
     * - gameId: string
     *
     * @param DeleteGameApiInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $gameId
     * @return JsonResponse
     */
    public function deleteGame(
        DeleteGameApiInterface $handler,
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

}
