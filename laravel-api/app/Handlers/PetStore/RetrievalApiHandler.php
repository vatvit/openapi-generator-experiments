<?php declare(strict_types=1);

namespace App\Handlers\PetStore;

use PetStoreApiV2\Server\Api\RetrievalApiInterface;

/**
 * Handler for RetrievalApi operations
 */
class RetrievalApiHandler implements RetrievalApiInterface
{
    use PetOperationsTrait;
}
