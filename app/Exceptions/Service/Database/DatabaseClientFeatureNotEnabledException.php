<?php

namespace Ruff\Exceptions\Service\Database;

use Ruff\Exceptions\RuffException;

class DatabaseClientFeatureNotEnabledException extends RuffException
{
    public function __construct()
    {
        parent::__construct('Client database creation is not enabled in this Panel.');
    }
}
