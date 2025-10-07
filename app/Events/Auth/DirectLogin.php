<?php

namespace Ruff\Events\Auth;

use Ruff\Models\User;
use Ruff\Events\Event;

class DirectLogin extends Event
{
    public function __construct(public User $user, public bool $remember)
    {
    }
}
