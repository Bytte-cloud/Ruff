<?php

namespace Ruff\Events\Server;

use Ruff\Events\Event;
use Ruff\Models\Server;
use Illuminate\Queue\SerializesModels;

class Created extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Server $server)
    {
    }
}
