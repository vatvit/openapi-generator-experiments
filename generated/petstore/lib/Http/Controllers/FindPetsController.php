<?php declare(strict_types=1);

namespace PetStoreApiV2\Server\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PetStoreApiV2\Server\Api\FindPetsApiInterface;
use PetStoreApiV2\Server\Http\Responses\FindPetsResponseInterface;
use Crell\Serde\SerdeCommon;

/**
 * FindPetsApiInterface Controller
 *
 * Generated server from OpenAPI specification
 * Uses dependency injection to call business logic handlers
 * Handlers return response models that enforce API specification
 */
class FindPetsController extends Controller
{
    /**
     * 
     *
     * Returns all pets from the system that the user has access to Nam sed condimentum est. Maecenas tempor sagittis sapien, nec rhoncus sem sagittis sit amet. Aenean at gravida augue, ac iaculis sem. Curabitur odio lorem, ornare eget elementum nec, cursus id lectus. Duis mi turpis, pulvinar ac eros ac, tincidunt varius justo. In hac habitasse platea dictumst. Integer at adipiscing ante, a sagittis ligula. Aenean pharetra tempor ante molestie imperdiet. Vivamus id aliquam diam. Cras quis velit non tortor eleifend sagittis. Praesent at enim pharetra urna volutpat venenatis eget eget mauris. In eleifend fermentum facilisis. Praesent enim enim, gravida ac sodales sed, placerat id erat. Suspendisse lacus dolor, consectetur non augue vel, vehicula interdum libero. Morbi euismod sagittis libero sed lacinia.  Sed tempus felis lobortis leo pulvinar rutrum. Nam mattis velit nisl, eu condimentum ligula luctus nec. Phasellus semper velit eget aliquet faucibus. In a mattis elit. Phasellus vel urna viverra, condimentum lorem id, rhoncus nibh. Ut pellentesque posuere elementum. Sed a varius odio. Morbi rhoncus ligula libero, vel eleifend nunc tristique vitae. Fusce et sem dui. Aenean nec scelerisque tortor. Fusce malesuada accumsan magna vel tempus. Quisque mollis felis eu dolor tristique, sit amet auctor felis gravida. Sed libero lorem, molestie sed nisl in, accumsan tempor nisi. Fusce sollicitudin massa ut lacinia mattis. Sed vel eleifend lorem. Pellentesque vitae felis pretium, pulvinar elit eu, euismod sapien.
     *
     * Query parameters validation (from OpenAPI spec):
     * - tags: string[]
     * - limit: int
     *
     * @param FindPetsApiInterface $handler Injected business logic handler
     * @param Request $request
     * @return JsonResponse
     */
    public function findPets(
        FindPetsApiInterface $handler,
        Request $request
    ): JsonResponse
    {
        // Validate request using generated rules
        $validated = $request->validate($this->findPetsValidationRules());

        // Extract validated parameters
        $tags = $request->query('tags', null);
        $limit = $request->query('limit', null);
        // Cast to int if present
        if ($limit !== null) {
            $limit = (int) $limit;
        }

        // Call handler with validated parameters
        $response = $handler->handle(
            $tags,
            $limit
        );

        // Convert response model to JSON (enforced by interface)
        return $response->toJsonResponse();
    }


    /**
     * Get validation rules for findPets request
     * Generated from OpenAPI specification
     *
     * @return array
     */
    protected function findPetsValidationRules(): array
    {
        return [
            'tags' => 'sometimes|array',
            'limit' => 'sometimes|integer',
        ];
    }

}
