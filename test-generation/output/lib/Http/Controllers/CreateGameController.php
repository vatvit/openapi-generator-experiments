<?php declare(strict_types=1);

namespace TicTacToeApiTest\Server\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use TicTacToeApiTest\Server\Api\CreateGameHandlerInterface;
use TicTacToeApiTest\Server\Api\CreateGameResponseInterface;
use \TicTacToeApiTest\Server\Models\CreateGameRequest;
use Crell\Serde\SerdeCommon;

/**
 * CreateGameApiInterface Controller
 *
 * Generated server from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class CreateGameController extends Controller
{
    /**
     * Create a new game
     *
     * Creates a new TicTacToe game with specified configuration.
     *
     * Request body validation (from OpenAPI spec):
     * - createGameRequest: required, \TicTacToeApiTest\Server\Models\CreateGameRequest
     *
     * @param CreateGameHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @return JsonResponse
     */
    public function createGame(
        CreateGameHandlerInterface $handler,
        Request $request
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->createGameValidationRules());

        // Extract validated parameters
        // Deserialize request body to \TicTacToeApiTest\Server\Models\CreateGameRequest model
        $serde = new SerdeCommon();
        $createGameRequest = $serde->deserialize($request->getContent(), from: 'json', to: \TicTacToeApiTest\Server\Models\CreateGameRequest::class);

        // Call handler with validated parameters
        $response = $handler->handle(
            $createGameRequest
        );

        // Convert response model to JSON (enforced by interface)
        return $response->toJsonResponse();
    }


    /**
     * Get validation rules for createGame request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function createGameValidationRules(): array
    {
        return [
            'mode' => 'required',
            'opponentId' => 'sometimes|string',
            'isPrivate' => 'sometimes|boolean',
            'metadata' => 'sometimes',
        ];
    }

}
