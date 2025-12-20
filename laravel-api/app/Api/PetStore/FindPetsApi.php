<?php declare(strict_types=1);

namespace App\Api\PetStore;

use PetStoreApiV2\Server\Api\FindPetsApiInterface;
use PetStoreApiV2\Server\Http\Responses\FindPetsApiInterfaceResponseInterface;
use PetStoreApiV2\Server\Http\Responses\FindPetsApiInterfaceResponseFactory;
use PetStoreApiV2\Server\Models\Pet;

/**
 * API for findPets operation
 * Implements business logic for listing pets
 */
class FindPetsApi implements FindPetsApiInterface
{
    public function handle(?array $tags, ?int $limit): FindPetsApiInterfaceResponseInterface
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
        return FindPetsApiInterfaceResponseFactory::status200($pets);
    }
}
