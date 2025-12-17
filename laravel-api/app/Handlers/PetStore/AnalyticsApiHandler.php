<?php declare(strict_types=1);

namespace App\Handlers\PetStore;

use PetStoreApiV2\Server\Api\AnalyticsApiInterface;

/**
 * Handler for AnalyticsApi operations
 */
class AnalyticsApiHandler implements AnalyticsApiInterface
{
    use PetOperationsTrait;
}
