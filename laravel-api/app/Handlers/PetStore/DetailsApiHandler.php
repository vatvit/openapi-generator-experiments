<?php declare(strict_types=1);

namespace App\Handlers\PetStore;

use PetStoreApiV2\Server\Api\DetailsApiInterface;

/**
 * Handler for DetailsApi operations
 */
class DetailsApiHandler implements DetailsApiInterface
{
    use PetOperationsTrait;
}
