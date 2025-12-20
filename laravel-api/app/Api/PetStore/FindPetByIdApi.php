<?php declare(strict_types=1);

namespace App\Api\PetStore;

use PetStoreApiV2\Server\Api\FindPetByIdApiInterface;
use PetStoreApiV2\Server\Http\Responses\FindPetByIdResponseInterface;
use PetStoreApiV2\Server\Http\Responses\FindPetById200Response;
use PetStoreApiV2\Server\Models\Pet;

/**
 * API for findPetById operation
 * Implements business logic for retrieving a single pet
 */
class FindPetByIdApi implements FindPetByIdApiInterface
{
    public function handle(int $id): FindPetByIdResponseInterface
    {
        // Business logic implementation
        $pet = new Pet(
            name: 'Sample Pet ' . $id,
            tag: 'demo',
            id: $id
        );

        // Return typed response (enforces API spec)
        return new FindPetById200Response($pet);
    }
}
