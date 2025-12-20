<?php declare(strict_types=1);

namespace TicTacToeApiV2\Server\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use TicTacToeApiV2\Server\Api\GetBoardApiInterface;
use TicTacToeApiV2\Server\Http\Responses\GetBoardResponseInterface;
use Crell\Serde\SerdeCommon;

/**
 * GetBoardApiInterface Controller
 *
 * Generated server from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class GetBoardController extends Controller
{
    /**
     * Get the game board
     *
     * Retrieves the current state of the board and the winner.
     *
     * Path parameters:
     * - gameId: string
     *
     * @param GetBoardApiInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $gameId
     * @return JsonResponse
     */
    public function getBoard(
        GetBoardApiInterface $handler,
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

}
