<?php

namespace Ruff\Http\Requests\Api\Application\Users;

use Ruff\Services\Acl\Api\AdminAcl as Acl;
use Ruff\Http\Requests\Api\Application\ApplicationApiRequest;

class GetUsersRequest extends ApplicationApiRequest
{
    protected ?string $resource = Acl::RESOURCE_USERS;

    protected int $permission = Acl::READ;
}
