<?php

namespace Ruff\Facades;

use Illuminate\Support\Facades\Facade;
use Ruff\Services\Activity\ActivityLogService;

class Activity extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ActivityLogService::class;
    }
}
