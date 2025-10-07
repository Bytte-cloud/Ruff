<?php

namespace Ruff\Events\User;

use Ruff\Models\User;
use Ruff\Events\Event;
use Illuminate\Queue\SerializesModels;

class Deleted extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public User $user)
    {
    }
}
