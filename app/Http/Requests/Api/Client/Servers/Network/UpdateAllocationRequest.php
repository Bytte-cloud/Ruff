<?php

namespace Ruff\Http\Requests\Api\Client\Servers\Network;

use Ruff\Models\Allocation;
use Ruff\Models\Permission;
use Ruff\Http\Requests\Api\Client\ClientApiRequest;

class UpdateAllocationRequest extends ClientApiRequest
{
    public function permission(): string
    {
        return Permission::ACTION_ALLOCATION_UPDATE;
    }

    public function rules(): array
    {
        $rules = Allocation::getRules();

        return [
            'notes' => array_merge($rules['notes'], ['present']),
        ];
    }
}
