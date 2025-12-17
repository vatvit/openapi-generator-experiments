<?php declare(strict_types=1);

namespace App\Handlers\PetStore;

use PetStoreApiV2\Server\Api\SearchApiInterface;

/**
 * Handler for SearchApi operations
 */
class SearchApiHandler implements SearchApiInterface
{
    use PetOperationsTrait;
}
