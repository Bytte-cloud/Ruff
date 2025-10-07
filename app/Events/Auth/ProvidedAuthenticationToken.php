<?php

namespace Ruff\Events\Auth;

use Ruff\Models\User;
use Ruff\Events\Event;

class ProvidedAuthenticationToken extends Event
{
    public function __construct(public User $user, public bool $recovery = false)
    {
    }
}
