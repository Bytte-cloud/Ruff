<?php

namespace Ruff\Http\Requests\Api\Client\Servers\Subusers;

use Ruff\Models\Permission;

class DeleteSubuserRequest extends SubuserRequest
{
    public function permission(): string
    {
        return Permission::ACTION_USER_DELETE;
    }
}
