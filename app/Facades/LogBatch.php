<?php

namespace Ruff\Facades;

use Illuminate\Support\Facades\Facade;
use Ruff\Services\Activity\ActivityLogBatchService;

class LogBatch extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ActivityLogBatchService::class;
    }
}
