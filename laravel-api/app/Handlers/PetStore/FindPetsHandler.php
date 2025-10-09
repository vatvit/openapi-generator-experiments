<?php declare(strict_types=1);

namespace App\Handlers\PetStore;

use PetStoreApiV2\Server\Api\FindPetsHandlerInterface;
use PetStoreApiV2\Server\Api\FindPetsResponseInterface;
use PetStoreApiV2\Server\Api\FindPets200Response;
use PetStoreApiV2\Server\Models\Pet;

/**
 * Handler for findPets operation (V2)
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
