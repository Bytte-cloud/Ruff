<?php

namespace Ruff\Http\Requests\Api\Client\Servers\Files;

use Ruff\Models\Permission;
use Ruff\Contracts\Http\ClientPermissionsRequest;
use Ruff\Http\Requests\Api\Client\ClientApiRequest;

class ChmodFilesRequest extends ClientApiRequest implements ClientPermissionsRequest
{
    public function permission(): string
    {
        return Permission::ACTION_FILE_UPDATE;
    }

    public function rules(): array
    {
        return [
            'root' => 'required|nullable|string',
            'files' => 'required|array',
            'files.*.file' => 'required|string',
            'files.*.mode' => 'required|numeric',
        ];
    }
}
