<?php declare(strict_types=1);

namespace PetStoreApiV2\Server\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PetStoreApiV2\Server\Api\DeletePetApiInterface;
use PetStoreApiV2\Server\Http\Responses\DeletePetResponseInterface;
use Crell\Serde\SerdeCommon;

/**
 * DeletePetApiInterface Controller
 *
 * Generated server from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class DeletePetController extends Controller
{
    /**
     * 
     *
     * deletes a single pet based on the ID supplied
     *
     * Path parameters:
     * - id: int
     *
     * @param DeletePetApiInterface $handler Injected business logic handler
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function deletePet(
        DeletePetApiInterface $handler,
        Request $request,
        int $id
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->deletePetValidationRules($id));

        // Extract validated parameters

        // Call handler with validated parameters
        $response = $handler->handle(
            $id
        );

        // Convert response model to JSON (enforced by interface)
        return $response->toJsonResponse();
    }


    /**
     * Get validation rules for deletePet request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function deletePetValidationRules(int $id): array
    {
        return [
        ];
    }

}
