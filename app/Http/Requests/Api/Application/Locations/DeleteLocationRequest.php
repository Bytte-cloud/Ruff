<?php

namespace Ruff\Http\Requests\Api\Application\Locations;

use Ruff\Services\Acl\Api\AdminAcl;
use Ruff\Http\Requests\Api\Application\ApplicationApiRequest;

class DeleteLocationRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_LOCATIONS;

    protected int $permission = AdminAcl::WRITE;
}
