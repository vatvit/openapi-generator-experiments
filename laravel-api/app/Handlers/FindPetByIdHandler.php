<?php declare(strict_types=1);

namespace App\Handlers;

use PetStoreApi\Scaffolding\Api\FindPetByIdHandlerInterface;
use PetStoreApi\Scaffolding\Api\FindPetByIdResponseInterface;
use PetStoreApi\Scaffolding\Api\FindPetById200Response;
use PetStoreApi\Scaffolding\Models\Pet;

/**
 * Handler for findPetById operation
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
