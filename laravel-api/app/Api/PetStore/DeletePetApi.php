<?php declare(strict_types=1);

namespace App\Api\PetStore;

use PetStoreApiV2\Server\Api\DeletePetApiInterface;
use PetStoreApiV2\Server\Http\Responses\DeletePetApiInterfaceResponseInterface;
use PetStoreApiV2\Server\Http\Responses\DeletePetApiInterfaceResponseFactory;
use PetStoreApiV2\Server\Models\NoContent204;

/**
 * API for deletePet operation
 * Implements business logic for deleting a pet
 */
class DeletePetApi implements DeletePetApiInterface
{
    public function handle(int $id): DeletePetApiInterfaceResponseInterface
    {
        // Business logic implementation - delete pet
        // For demo purposes, just return success

        // Return typed response (enforces API spec)
        return DeletePetApiInterfaceResponseFactory::status204(new NoContent204());
    }
}
