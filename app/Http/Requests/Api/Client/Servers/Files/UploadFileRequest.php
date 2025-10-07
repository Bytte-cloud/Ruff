<?php

namespace Ruff\Http\Requests\Api\Client\Servers\Files;

use Ruff\Models\Permission;
use Ruff\Http\Requests\Api\Client\ClientApiRequest;

class UploadFileRequest extends ClientApiRequest
{
    public function permission(): string
    {
        return Permission::ACTION_FILE_CREATE;
    }
}
