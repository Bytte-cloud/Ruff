<?php

namespace Ruff\Repositories\Eloquent;

use Ruff\Models\RecoveryToken;

class RecoveryTokenRepository extends EloquentRepository
{
    public function model(): string
    {
        return RecoveryToken::class;
    }
}
