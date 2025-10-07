<?php

namespace Ruff\Repositories\Eloquent;

use Ruff\Models\ServerVariable;
use Ruff\Contracts\Repository\ServerVariableRepositoryInterface;

class ServerVariableRepository extends EloquentRepository implements ServerVariableRepositoryInterface
{
    /**
     * Return the model backing this repository.
     */
    public function model(): string
    {
        return ServerVariable::class;
    }
}
