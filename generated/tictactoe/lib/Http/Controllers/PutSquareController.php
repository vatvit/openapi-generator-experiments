<?php declare(strict_types=1);

namespace TicTacToeApiV2\Server\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use TicTacToeApiV2\Server\Api\PutSquareApiInterface;
use TicTacToeApiV2\Server\Http\Responses\PutSquareResponseInterface;
use \TicTacToeApiV2\Server\Models\MoveRequest;
use Crell\Serde\SerdeCommon;

/**
 * PutSquareApiInterface Controller
 *
 * Generated server from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class PutSquareController extends Controller
{
    /**
     * Set a single board square
     *
     * Places a mark on the board and retrieves the whole board and the winner (if any).
     *
     * Request body validation (from OpenAPI spec):
     * - moveRequest: required, \TicTacToeApiV2\Server\Models\MoveRequest
     *
     * Path parameters:
     * - gameId: string
     * - row: int
     * - column: int
     *
     * @param PutSquareApiInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $gameId
     * @param int $row
     * @param int $column
     * @return JsonResponse
     */
    public function putSquare(
        PutSquareApiInterface $handler,
        Request $request,
        string $gameId,
        int $row,
        int $column
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->putSquareValidationRules($gameId, $row, $column));

        // Extract validated parameters
        // Deserialize request body to \TicTacToeApiV2\Server\Models\MoveRequest model
        $serde = new SerdeCommon();
        $moveRequest = $serde->deserialize($request->getContent(), from: 'json', to: \TicTacToeApiV2\Server\Models\MoveRequest::class);

        // Call handler with validated parameters
        $response = $handler->handle(
            $gameId,
            $row,
            $column,
            $moveRequest
        );

        // Convert response model to JSON (enforced by interface)
        return $response->toJsonResponse();
    }


    /**
     * Get validation rules for putSquare request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function putSquareValidationRules(string $gameId, int $row, int $column): array
    {
        return [
            'mark' => 'required',
        ];
    }

}
