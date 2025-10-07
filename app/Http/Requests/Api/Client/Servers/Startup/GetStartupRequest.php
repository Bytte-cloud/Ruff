<?php

namespace Ruff\Http\Requests\Api\Client\Servers\Startup;

use Ruff\Models\Permission;
use Ruff\Http\Requests\Api\Client\ClientApiRequest;

class GetStartupRequest extends ClientApiRequest
{
    public function permission(): string
    {
        return Permission::ACTION_STARTUP_READ;
    }
}
