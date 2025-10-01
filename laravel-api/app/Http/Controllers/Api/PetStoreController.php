<?php declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Pet Store Controller
 *
 * Implements business logic for Pet Store API
 * Routes are automatically generated from OpenAPI specification
 */
class PetStoreController extends Controller
{
    /**
     * Create a new pet
     *
     * Implements business logic for pet creation
     * Validation rules provided by scaffolding: addPetValidationRules()
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addPet(Request $request): JsonResponse
    {
        // Use validation rules from scaffolding
        $validated = $request->validate($this->addPetValidationRules());

        // For demo purposes, create a simple pet record
        $pet = [
            'id' => random_int(1, 1000),
            'name' => $request->input('name', 'Unknown Pet'),
            'tag' => $request->input('tag', 'general'),
            'status' => 'available'
        ];

        return response()->json([
            'data' => $pet
        ], 201);
    }

    /**
     * Delete pet by ID
     *
     * Implements business logic for pet deletion
     * Path parameter validation provided by scaffolding: validateDeletePetPathParams()
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function deletePet(Request $request, int $id): JsonResponse
    {
        // Use path parameter validation from scaffolding
        if (!$this->validatedeletePetPathParams($id)) {
            return response()->json([
                'message' => 'Invalid pet ID'
            ], 400);
        }

        // Business logic implementation
        // For demo purposes, just return success
        return response()->json(null, 204);
    }

    /**
     * Get pet by ID
     *
     * Implements business logic for pet retrieval
     * Path parameter validation provided by scaffolding: validateFindPetByIdPathParams()
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function findPetById(Request $request, int $id): JsonResponse
    {
        // Use path parameter validation from scaffolding
        if (!$this->validatefindPetByIdPathParams($id)) {
            return response()->json([
                'message' => 'Invalid pet ID'
            ], 400);
        }

        // Business logic implementation
        $pet = [
            'id' => $id,
            'name' => 'Sample Pet ' . $id,
            'tag' => 'demo',
            'status' => 'available'
        ];

        return response()->json([
            'data' => $pet
        ]);
    }

    /**
     * List all pets
     *
     * Implements business logic for pet listing
     * Validation rules provided by scaffolding: findPetsValidationRules()
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function findPets(Request $request): JsonResponse
    {
        // Business logic implementation
        $limit = $request->input('limit', 10);
        $tags = $request->input('tags', []);

        $pets = [];
        for ($i = 1; $i <= $limit; $i++) {
            $pets[] = [
                'id' => $i,
                'name' => 'Pet ' . $i,
                'tag' => !empty($tags) ? $tags[0] : 'demo',
                'status' => 'available'
            ];
        }

        return response()->json([
            'data' => $pets,
            'meta' => [
                'total' => $limit,
                'limit' => $limit
            ]
        ]);
    }
}