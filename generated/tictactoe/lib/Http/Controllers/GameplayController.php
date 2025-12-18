<?php declare(strict_types=1);

namespace TicTacToeApiV2\Server\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use TicTacToeApiV2\Server\Api\GetBoardHandlerInterface;
use TicTacToeApiV2\Server\Api\GetBoardResponseInterface;
use TicTacToeApiV2\Server\Api\GetGameHandlerInterface;
use TicTacToeApiV2\Server\Api\GetGameResponseInterface;
use TicTacToeApiV2\Server\Api\GetMovesHandlerInterface;
use TicTacToeApiV2\Server\Api\GetMovesResponseInterface;
use TicTacToeApiV2\Server\Api\GetSquareHandlerInterface;
use TicTacToeApiV2\Server\Api\GetSquareResponseInterface;
use TicTacToeApiV2\Server\Api\PutSquareHandlerInterface;
use TicTacToeApiV2\Server\Api\PutSquareResponseInterface;
use \TicTacToeApiV2\Server\Models\MoveRequest;
use Crell\Serde\SerdeCommon;

/**
 * GameplayApiInterface Controller
 *
 * Generated server from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class GameplayController extends Controller
{
    /**
     * Get the game board
     *
     * Retrieves the current state of the board and the winner.
     *
     * Path parameters:
     * - gameId: string
     *
     * @param GetBoardHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $gameId
     * @return JsonResponse
     */
    public function getBoard(
        GetBoardHandlerInterface $handler,
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
     * Get game details
     *
     * Retrieves detailed information about a specific game.
     *
     * Path parameters:
     * - gameId: string
     *
     * @param GetGameHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $gameId
     * @return JsonResponse
     */
    public function getGame(
        GetGameHandlerInterface $handler,
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
     * Get move history
     *
     * Retrieves the complete move history for a game.
     *
     * Path parameters:
     * - gameId: string
     *
     * @param GetMovesHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $gameId
     * @return JsonResponse
     */
    public function getMoves(
        GetMovesHandlerInterface $handler,
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
     * Get a single board square
     *
     * Retrieves the requested square.
     *
     * Path parameters:
     * - gameId: string
     * - row: int
     * - column: int
     *
     * @param GetSquareHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $gameId
     * @param int $row
     * @param int $column
     * @return JsonResponse
     */
    public function getSquare(
        GetSquareHandlerInterface $handler,
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
     * @param PutSquareHandlerInterface $handler Injected business logic handler
     * @param Request $request
     * @param string $gameId
     * @param int $row
     * @param int $column
     * @return JsonResponse
     */
    public function putSquare(
        PutSquareHandlerInterface $handler,
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
