<?php declare(strict_types=1);

namespace PetStoreApiV2\Server\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PetStoreApiV2\Server\Api\AddPetApiInterface;
use PetStoreApiV2\Server\Http\Responses\AddPetResponseInterface;
use \PetStoreApiV2\Server\Models\NewPet;
use Crell\Serde\SerdeCommon;

/**
 * AddPetApiInterface Controller
 *
 * Generated server from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class AddPetController extends Controller
{
    /**
     * 
     *
     * Creates a new pet in the store. Duplicates are allowed
     *
     * Request body validation (from OpenAPI spec):
     * - newPet: required, \PetStoreApiV2\Server\Models\NewPet
     *
     * @param AddPetApiInterface $handler Injected business logic handler
     * @param Request $request
     * @return JsonResponse
     */
    public function addPet(
        AddPetApiInterface $handler,
        Request $request
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->addPetValidationRules());

        // Extract validated parameters
        // Deserialize request body to \PetStoreApiV2\Server\Models\NewPet model
        $serde = new SerdeCommon();
        $newPet = $serde->deserialize($request->getContent(), from: 'json', to: \PetStoreApiV2\Server\Models\NewPet::class);

        // Call handler with validated parameters
        $response = $handler->handle(
            $newPet
        );

        // Convert response model to JSON (enforced by interface)
        return $response->toJsonResponse();
    }


    /**
     * Get validation rules for addPet request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function addPetValidationRules(): array
    {
        return [
            'name' => 'required|string',
            'tag' => 'sometimes|string',
        ];
    }

}
