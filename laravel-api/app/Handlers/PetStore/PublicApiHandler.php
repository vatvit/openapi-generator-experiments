<?php declare(strict_types=1);

namespace App\Handlers\PetStore;

use PetStoreApiV2\Server\Api\PublicApiInterface;

/**
 * Handler for PublicApi operations
 */
class PublicApiHandler implements PublicApiInterface
{
    use PetOperationsTrait;
}
