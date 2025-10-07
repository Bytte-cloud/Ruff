<?php

namespace Ruff\Http\Requests\Api\Application\Nests\Eggs;

use Ruff\Services\Acl\Api\AdminAcl;
use Ruff\Http\Requests\Api\Application\ApplicationApiRequest;

class GetEggRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_EGGS;

    protected int $permission = AdminAcl::READ;
}
