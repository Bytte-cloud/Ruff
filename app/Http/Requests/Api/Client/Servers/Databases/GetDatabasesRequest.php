<?php

namespace Ruff\Http\Requests\Api\Client\Servers\Databases;

use Ruff\Models\Permission;
use Ruff\Contracts\Http\ClientPermissionsRequest;
use Ruff\Http\Requests\Api\Client\ClientApiRequest;

class GetDatabasesRequest extends ClientApiRequest implements ClientPermissionsRequest
{
    public function permission(): string
    {
        return Permission::ACTION_DATABASE_READ;
    }
}
