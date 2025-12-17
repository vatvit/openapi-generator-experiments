<?php declare(strict_types=1);

namespace App\Handlers\PetStore;

use PetStoreApiV2\Server\Api\ManagementApiInterface;

/**
 * Handler for ManagementApi operations
 */
class ManagementApiHandler implements ManagementApiInterface
{
    use PetOperationsTrait;
}
