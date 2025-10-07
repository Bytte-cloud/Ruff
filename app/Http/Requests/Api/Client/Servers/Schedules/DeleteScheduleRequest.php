<?php

namespace Ruff\Http\Requests\Api\Client\Servers\Schedules;

use Ruff\Models\Permission;

class DeleteScheduleRequest extends ViewScheduleRequest
{
    public function permission(): string
    {
        return Permission::ACTION_SCHEDULE_DELETE;
    }
}
