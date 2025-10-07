<?php

namespace Ruff\Facades;

use Illuminate\Support\Facades\Facade;
use Ruff\Services\Activity\ActivityLogTargetableService;

class LogTarget extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ActivityLogTargetableService::class;
    }
}
