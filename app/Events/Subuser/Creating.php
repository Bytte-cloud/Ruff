<?php

namespace Ruff\Events\Subuser;

use Ruff\Events\Event;
use Ruff\Models\Subuser;
use Illuminate\Queue\SerializesModels;

class Creating extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Subuser $subuser)
    {
    }
}
