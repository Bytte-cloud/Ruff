<?php

namespace Ruff\Listeners\Auth;

use Ruff\Facades\Activity;
use Ruff\Events\Auth\ProvidedAuthenticationToken;

class TwoFactorListener
{
    public function handle(ProvidedAuthenticationToken $event): void
    {
        Activity::event($event->recovery ? 'auth:recovery-token' : 'auth:token')
            ->withRequestMetadata()
            ->subject($event->user)
            ->log();
    }
}
