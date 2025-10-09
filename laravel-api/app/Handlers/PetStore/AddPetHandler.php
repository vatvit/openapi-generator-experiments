<?php declare(strict_types=1);

namespace App\Handlers\PetStore;

use PetStoreApiV2\Server\Api\AddPetHandlerInterface;
use PetStoreApiV2\Server\Api\AddPetResponseInterface;
use PetStoreApiV2\Server\Api\AddPet200Response;
use PetStoreApiV2\Server\Models\NewPet;
use PetStoreApiV2\Server\Models\Pet;

/**
 * Handler for addPet operation (V2)
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
