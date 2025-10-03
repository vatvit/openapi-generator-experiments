<?php declare(strict_types=1);

namespace App\Handlers;

use PetStoreApi\Scaffolding\Api\FindPetsHandlerInterface;
use PetStoreApi\Scaffolding\Api\FindPetsResponseInterface;
use PetStoreApi\Scaffolding\Api\FindPets200Response;
use PetStoreApi\Scaffolding\Models\Pet;

/**
 * Handler for findPets operation
 * Implements business logic for listing pets
 */
class FindPetsHandler implements FindPetsHandlerInterface
{
    public function handle(?array $tags, ?int $limit): FindPetsResponseInterface
    {
        // Business logic implementation
        $limit = $limit ?? 10;

        $pets = [];
        for ($i = 1; $i <= $limit; $i++) {
            $pets[] = new Pet(
                name: 'Pet ' . $i,
                tag: !empty($tags) ? $tags[0] : 'demo',
                id: $i
            );
        }

        // Return typed response (enforces API spec)
        return new FindPets200Response($pets);
    }
}
