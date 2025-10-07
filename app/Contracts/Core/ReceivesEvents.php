<?php

namespace Ruff\Contracts\Core;

use Ruff\Events\Event;

interface ReceivesEvents
{
    /**
     * Handles receiving an event from the application.
     */
    public function handle(Event $notification): void;
}
