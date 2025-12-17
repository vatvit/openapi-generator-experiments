<?php declare(strict_types=1);

namespace App\Handlers\PetStore;

use PetStoreApiV2\Server\Api\PetsApiInterface;

/**
 * Handler for PetsApi operations
 */
class PetsApiHandler implements PetsApiInterface
{
    use PetOperationsTrait;
}
