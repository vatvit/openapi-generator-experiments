<?php declare(strict_types=1);

namespace App\Handlers\PetStore;

use PetStoreApiV2\Server\Api\ReportingApiInterface;

/**
 * Handler for ReportingApi operations
 */
class ReportingApiHandler implements ReportingApiInterface
{
    use PetOperationsTrait;
}
