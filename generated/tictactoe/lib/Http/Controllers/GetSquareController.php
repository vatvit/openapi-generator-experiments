<?php declare(strict_types=1);

namespace TicTacToeApiV2\Server\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use TicTacToeApiV2\Server\Api\GetSquareApiInterface;
use TicTacToeApiV2\Server\Http\Responses\GetSquareResponseInterface;
use Crell\Serde\SerdeCommon;

/**
 * GetSquareApiInterface Controller
 *
 * Generated server from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class GetSquareController extends Controller
{
    /**
     * Get a single board square
     *
     * Retrieves the requested square.
     *
     * Path parameters:
     * - gameId: string
     * - row: int
     * - column: int
     *
     * @param GetSquareApiInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $gameId
     * @param int $row
     * @param int $column
     * @return JsonResponse
     */
    public function getSquare(
        GetSquareApiInterface $handler,
        Request $request,
        string $gameId,
        int $row,
        int $column
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->getSquareValidationRules($gameId, $row, $column));

        // Extract validated parameters

        // Call handler with validated parameters
        $response = $handler->handle(
            $gameId,
            $row,
            $column
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
    protected function getSquareValidationRules(string $gameId, int $row, int $column): array
    {
        return [
        ];
    }

}
