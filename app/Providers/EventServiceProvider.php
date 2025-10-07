<?php

namespace Ruff\Providers;

use Ruff\Models\User;
use Ruff\Models\Server;
use Ruff\Models\Subuser;
use Ruff\Models\EggVariable;
use Ruff\Observers\UserObserver;
use Ruff\Observers\ServerObserver;
use Ruff\Observers\SubuserObserver;
use Ruff\Observers\EggVariableObserver;
use Ruff\Listeners\Auth\AuthenticationListener;
use Ruff\Events\Server\Installed as ServerInstalledEvent;
use Ruff\Notifications\ServerInstalled as ServerInstalledNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     */
    protected $listen = [
        ServerInstalledEvent::class => [ServerInstalledNotification::class],
    ];

    protected $subscribe = [
        AuthenticationListener::class,
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();

        User::observe(UserObserver::class);
        Server::observe(ServerObserver::class);
        Subuser::observe(SubuserObserver::class);
        EggVariable::observe(EggVariableObserver::class);
    }
}
