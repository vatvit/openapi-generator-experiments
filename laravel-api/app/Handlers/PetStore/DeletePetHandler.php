<?php declare(strict_types=1);

namespace App\Handlers\PetStore;

use PetStoreApiV2\Server\Api\DeletePetHandlerInterface;
use PetStoreApiV2\Server\Api\DeletePetResponseInterface;
use PetStoreApiV2\Server\Api\DeletePet204Response;
use PetStoreApiV2\Server\Models\NoContent204;

/**
 * Handler for deletePet operation (V2)
 * Implements business logic for deleting a pet
 */
class DeletePetHandler implements DeletePetHandlerInterface
{
    public function handle(int $id): DeletePetResponseInterface
    {
        // Business logic implementation - delete pet
        // For demo purposes, just return success

        // Return typed response (enforces API spec)
        return new DeletePet204Response(new NoContent204());
    }
}
