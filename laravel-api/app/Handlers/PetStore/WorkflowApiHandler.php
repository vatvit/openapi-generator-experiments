<?php declare(strict_types=1);

namespace App\Handlers\PetStore;

use PetStoreApiV2\Server\Api\WorkflowApiInterface;

/**
 * Handler for WorkflowApi operations
 */
class WorkflowApiHandler implements WorkflowApiInterface
{
    use PetOperationsTrait;
}
