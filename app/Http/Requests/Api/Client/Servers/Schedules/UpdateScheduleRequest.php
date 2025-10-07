<?php

namespace Ruff\Http\Requests\Api\Client\Servers\Schedules;

use Ruff\Models\Permission;

class UpdateScheduleRequest extends StoreScheduleRequest
{
    public function permission(): string
    {
        return Permission::ACTION_SCHEDULE_UPDATE;
    }
}
