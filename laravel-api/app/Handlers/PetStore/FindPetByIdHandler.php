<?php declare(strict_types=1);

namespace App\Handlers\PetStore;

use PetStoreApiV2\Server\Api\FindPetByIdHandlerInterface;
use PetStoreApiV2\Server\Api\FindPetByIdResponseInterface;
use PetStoreApiV2\Server\Api\FindPetById200Response;
use PetStoreApiV2\Server\Models\Pet;

/**
 * Handler for findPetById operation (V2)
 * Implements business logic for retrieving a single pet
 */
class FindPetByIdHandler implements FindPetByIdHandlerInterface
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
