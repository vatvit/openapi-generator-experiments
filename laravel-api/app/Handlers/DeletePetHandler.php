<?php declare(strict_types=1);

namespace App\Handlers;

use PetStoreApi\Scaffolding\Api\DeletePetHandlerInterface;
use PetStoreApi\Scaffolding\Api\DeletePetResponseInterface;
use PetStoreApi\Scaffolding\Api\DeletePet204Response;
use PetStoreApi\Scaffolding\Models\NoContent204;

/**
 * Handler for deletePet operation
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
