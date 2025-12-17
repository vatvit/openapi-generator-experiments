<?php declare(strict_types=1);

namespace App\Handlers\PetStore;

use PetStoreApiV2\Server\Api\CreationApiInterface;

/**
 * Handler for CreationApi operations
 */
class CreationApiHandler implements CreationApiInterface
{
    use PetOperationsTrait;
}
