<?php declare(strict_types=1);

namespace TicTacToeApi\Scaffolding\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use TicTacToeApi\Scaffolding\Api\GetBoardHandlerInterface;
use TicTacToeApi\Scaffolding\Api\GetBoardResponseInterface;
use TicTacToeApi\Scaffolding\Api\GetSquareHandlerInterface;
use TicTacToeApi\Scaffolding\Api\GetSquareResponseInterface;
use TicTacToeApi\Scaffolding\Api\PutSquareHandlerInterface;
use TicTacToeApi\Scaffolding\Api\PutSquareResponseInterface;
use Crell\Serde\SerdeCommon;

/**
 * GameplayApiInterface Controller
 *
 * Generated scaffolding from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class GameplayController extends Controller
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

    /**
     * Get a single board square
     *
     * Retrieves the requested square.
     *
     * Path parameters:
     * - row: int
     * - column: int
     *
     * @param GetSquareHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @param int $row
     * @param int $column
     * @return JsonResponse
     */
    public function getSquare(
        GetSquareHandlerInterface $handler,
        Request $request,
        int $row,
        int $column
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->getSquareValidationRules($row, $column));

        // Extract validated parameters

        // Call handler with validated parameters
        $response = $handler->handle(
            $row,
            $column
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
     * - body: required, string
     *
     * Path parameters:
     * - row: int
     * - column: int
     *
     * @param PutSquareHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @param int $row
     * @param int $column
     * @return JsonResponse
     */
    public function putSquare(
        PutSquareHandlerInterface $handler,
        Request $request,
        int $row,
        int $column
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->putSquareValidationRules($row, $column));

        // Extract validated parameters
        // Extract primitive body parameter
        $body = json_decode($request->getContent(), true);

        // Call handler with validated parameters
        $response = $handler->handle(
            $row,
            $column,
            $body
        );

        // Convert response model to JSON (enforced by interface)
        return $response->toJsonResponse();
    }


    /**
     * Get validation rules for getSquare request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function getSquareValidationRules(int $row, int $column): array
    {
        return [
        ];
    }

    /**
     * Get validation rules for putSquare request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function putSquareValidationRules(int $row, int $column): array
    {
        return [
        ];
    }

}
