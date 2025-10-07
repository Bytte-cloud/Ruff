<?php

namespace Ruff\Http\Requests\Api\Application\Servers\Databases;

use Ruff\Services\Acl\Api\AdminAcl;

class ServerDatabaseWriteRequest extends GetServerDatabasesRequest
{
    protected int $permission = AdminAcl::WRITE;
}
