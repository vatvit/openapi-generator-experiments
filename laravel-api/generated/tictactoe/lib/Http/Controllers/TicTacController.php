<?php declare(strict_types=1);

namespace TicTacToeApi\Scaffolding\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use TicTacToeApi\Scaffolding\Api\GetBoardHandlerInterface;
use TicTacToeApi\Scaffolding\Api\GetBoardResponseInterface;
use Crell\Serde\SerdeCommon;

/**
 * TicTacApiInterface Controller
 *
 * Generated scaffolding from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class TicTacController extends Controller
{
    /**
     * Get the whole board
     *
     * Retrieves the current state of the board and the winner.
     *
     * @param GetBoardHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @return JsonResponse
     */
    public function getBoard(
        GetBoardHandlerInterface $handler,
        Request $request
    ): JsonResponse
    {
        // Validate request using generated rules

        // Extract validated parameters

        // Call handler with validated parameters
        $response = $handler->handle(
        );

        // Convert response model to JSON (enforced by interface)
        return $response->toJsonResponse();
    }


}
