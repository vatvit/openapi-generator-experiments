<?php declare(strict_types=1);

namespace App\Handlers\PetStore;

use PetStoreApiV2\Server\Api\InventoryApiInterface;

/**
 * Handler for InventoryApi operations
 */
class InventoryApiHandler implements InventoryApiInterface
{
    use PetOperationsTrait;
}
