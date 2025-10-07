<?php

namespace Ruff\Repositories\Eloquent;

use Ruff\Models\User;
use Ruff\Contracts\Repository\UserRepositoryInterface;

class UserRepository extends EloquentRepository implements UserRepositoryInterface
{
    /**
     * Return the model backing this repository.
     */
    public function model(): string
    {
        return User::class;
    }
}
