<?php declare(strict_types=1);

namespace App\Handlers\PetStore;

use PetStoreApiV2\Server\Api\AdminApiInterface;

/**
 * Handler for AdminApi operations
 */
class AdminApiHandler implements AdminApiInterface
{
    use PetOperationsTrait;
}
