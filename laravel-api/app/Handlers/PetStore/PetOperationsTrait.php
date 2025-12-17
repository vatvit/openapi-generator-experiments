<?php declare(strict_types=1);

namespace App\Handlers\PetStore;

use PetStoreApiV2\Server\Models\Pet;
use PetStoreApiV2\Server\Models\NewPet;
use PetStoreApiV2\Server\Models\Error;

/**
 * Shared pet operations for all PetStore API handlers
 */
trait PetOperationsTrait
{
    public function findPets(?array $tags, ?int $limit): array | Error
    {
        // Mock implementation - return sample pets
        return [
            new Pet(
                id: 1,
                name: 'Fluffy',
                tag: 'cat'
            ),
            new Pet(
                id: 2,
                name: 'Buddy',
                tag: 'dog'
            )
        ];
    }

    public function findPetById(int $id): Pet | Error
    {
        // Mock implementation - return sample pet
        return new Pet(
            id: $id,
            name: 'Sample Pet',
            tag: 'cat'
        );
    }

    public function addPet(NewPet $newPet): Pet | Error
    {
        // Mock implementation - create pet with generated ID
        return new Pet(
            id: rand(1000, 9999),
            name: $newPet->name,
            tag: $newPet->tag ?? null
        );
    }

    public function deletePet(int $id): \PetStoreApiV2\Server\Models\NoContent204 | Error
    {
        // Mock implementation - return 204 No Content
        return new \PetStoreApiV2\Server\Models\NoContent204();
    }
}
