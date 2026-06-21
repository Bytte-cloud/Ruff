<?php

namespace Addons\HelloWorld;

use Illuminate\Support\ServiceProvider;

/**
 * Example addon service provider. Registered automatically by the panel while the
 * "hello-world" addon is enabled (Admin → Addons). Wire up routes, views,
 * bindings and console commands here exactly as you would in a Laravel package.
 */
class HelloWorldServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}
