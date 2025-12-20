<?php declare(strict_types=1);

namespace App\Api\PetStore;

use PetStoreApiV2\Server\Api\DeletePetApiInterface;
use PetStoreApiV2\Server\Http\Responses\DeletePetResponseInterface;
use PetStoreApiV2\Server\Http\Responses\DeletePet204Response;
use PetStoreApiV2\Server\Models\NoContent204;

/**
 * API for deletePet operation
 * Implements business logic for deleting a pet
 */
class DeletePetApi implements DeletePetApiInterface
{
    public function handle(int $id): DeletePetResponseInterface
    {
        // Business logic implementation - delete pet
        // For demo purposes, just return success

        // Return typed response (enforces API spec)
        return new DeletePet204Response(new NoContent204());
    }
}
