<?php declare(strict_types=1);

namespace PetStoreApiV2\Server\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PetStoreApiV2\Server\Api\FindPetByIdApiInterface;
use PetStoreApiV2\Server\Http\Responses\FindPetByIdResponseInterface;
use Crell\Serde\SerdeCommon;

/**
 * FindPetByIdApiInterface Controller
 *
 * Generated server from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class FindPetByIdController extends Controller
{
    /**
     * 
     *
     * Returns a user based on a single ID, if the user does not have access to the pet
     *
     * Path parameters:
     * - id: int
     *
     * @param FindPetByIdApiInterface $handler Injected business logic handler
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function findPetById(
        FindPetByIdApiInterface $handler,
        Request $request,
        int $id
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->findPetByIdValidationRules($id));

        // Extract validated parameters

        // Call handler with validated parameters
        $response = $handler->handle(
            $id
        );

        // Convert response model to JSON (enforced by interface)
        return $response->toJsonResponse();
    }


    /**
     * Get validation rules for findPetById request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function findPetByIdValidationRules(int $id): array
    {
        return [
        ];
    }

}
