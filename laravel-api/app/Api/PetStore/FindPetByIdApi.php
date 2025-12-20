<?php declare(strict_types=1);

namespace App\Api\PetStore;

use PetStoreApiV2\Server\Api\FindPetByIdApiInterface;
use PetStoreApiV2\Server\Http\Responses\FindPetByIdApiInterfaceResponseInterface;
use PetStoreApiV2\Server\Http\Responses\FindPetByIdApiInterfaceResponseFactory;
use PetStoreApiV2\Server\Models\Pet;

/**
 * API for findPetById operation
 * Implements business logic for retrieving a single pet
 */
class FindPetByIdApi implements FindPetByIdApiInterface
{
    public function handle(int $id): FindPetByIdApiInterfaceResponseInterface
    {
        // Business logic implementation
        $pet = new Pet(
            name: 'Sample Pet ' . $id,
            tag: 'demo',
            id: $id
        );

        // Return typed response (enforces API spec)
        return FindPetByIdApiInterfaceResponseFactory::status200($pet);
    }
}
