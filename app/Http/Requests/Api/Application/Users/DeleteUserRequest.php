<?php

namespace Ruff\Http\Requests\Api\Application\Users;

use Ruff\Services\Acl\Api\AdminAcl;
use Ruff\Http\Requests\Api\Application\ApplicationApiRequest;

class DeleteUserRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_USERS;

    protected int $permission = AdminAcl::WRITE;
}
