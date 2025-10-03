<?php declare(strict_types=1);

namespace App\Handlers;

use PetStoreApi\Scaffolding\Api\AddPetHandlerInterface;
use PetStoreApi\Scaffolding\Api\AddPetResponseInterface;
use PetStoreApi\Scaffolding\Api\AddPet200Response;
use PetStoreApi\Scaffolding\Models\NewPet;
use PetStoreApi\Scaffolding\Models\Pet;

/**
 * Handler for addPet operation
 * Implements business logic for creating a new pet
 */
class AddPetHandler implements AddPetHandlerInterface
{
    public function handle(NewPet $newPet): AddPetResponseInterface
    {
        // Business logic implementation - create pet with new ID
        $pet = new Pet(
            name: $newPet->name,
            tag: $newPet->tag,
            id: random_int(1, 1000)
        );

        // Return typed response (enforces API spec)
        return new AddPet200Response($pet);
    }
}
