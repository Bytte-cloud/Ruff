<?php

namespace Ruff\Http\Requests\Api\Client\Servers\Settings;

use Ruff\Models\Permission;
use Ruff\Http\Requests\Api\Client\ClientApiRequest;

class ReinstallServerRequest extends ClientApiRequest
{
    public function permission(): string
    {
        return Permission::ACTION_SETTINGS_REINSTALL;
    }
}
