<?php

namespace Ruff\Http\Requests\Api\Application\Servers\Databases;

use Ruff\Services\Acl\Api\AdminAcl;
use Ruff\Http\Requests\Api\Application\ApplicationApiRequest;

class GetServerDatabaseRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_SERVER_DATABASES;

    protected int $permission = AdminAcl::READ;
}
