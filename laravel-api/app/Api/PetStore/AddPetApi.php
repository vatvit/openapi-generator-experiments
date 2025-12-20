<?php declare(strict_types=1);

namespace App\Api\PetStore;

use PetStoreApiV2\Server\Api\AddPetApiInterface;
use PetStoreApiV2\Server\Http\Responses\AddPetApiInterfaceResponseInterface;
use PetStoreApiV2\Server\Http\Responses\AddPetApiInterfaceResponseFactory;
use PetStoreApiV2\Server\Models\NewPet;
use PetStoreApiV2\Server\Models\Pet;

/**
 * API for addPet operation
 * Implements business logic for creating a new pet
 */
class AddPetApi implements AddPetApiInterface
{
    public function handle(NewPet $newPet): AddPetApiInterfaceResponseInterface
    {
        // Business logic implementation - create pet with new ID
        $pet = new Pet(
            name: $newPet->name,
            tag: $newPet->tag,
            id: random_int(1, 1000)
        );

        // Return typed response (enforces API spec)
        return AddPetApiInterfaceResponseFactory::status200($pet);
    }
}
