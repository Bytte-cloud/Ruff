<?php

namespace Ruff\Providers;

use Ruff\Services\Addons\AddonManager;
use Illuminate\Support\ServiceProvider;

/**
 * Wires drop-in addons into the application:
 *
 *  - registers PSR-4 autoloading for every discovered addon so its classes load;
 *  - for each ENABLED addon, registers its service provider (where it declares
 *    its own routes/views/bindings, exactly like a Laravel package) and makes
 *    its migrations visible to `php artisan migrate`.
 *
 * This provider is registered in config/app.php. The addons themselves live
 * outside the autoloader Composer knows about, hence the runtime PSR-4 hook.
 */
class AddonServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AddonManager::class);

        // Map each addon's declared namespace to its src/ directory and register
        // a single autoloader for all of them. We register every discovered addon
        // (not only enabled ones) so the admin panel can introspect manifests and
        // so a freshly-dropped-in addon's provider can be resolved on enable.
        $map = [];
        foreach ($this->app->make(AddonManager::class)->manifests() as $manifest) {
            if (!empty($manifest['namespace']) && !empty($manifest['id'])) {
                $prefix = rtrim($manifest['namespace'], '\\') . '\\';
                $map[$prefix] = $this->app->basePath('addons/' . $manifest['id'] . '/src');
            }
        }

        if (!empty($map)) {
            spl_autoload_register(function (string $class) use ($map) {
                foreach ($map as $prefix => $directory) {
                    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
                        continue;
                    }
                    $relative = substr($class, strlen($prefix));
                    $path = $directory . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
                    if (is_file($path)) {
                        require $path;
                    }

                    return;
                }
            });
        }
    }

    public function boot(): void
    {
        /** @var AddonManager $manager */
        $manager = $this->app->make(AddonManager::class);

        foreach ($manager->enabledManifests() as $manifest) {
            // Make the addon's migrations part of `php artisan migrate`.
            $migrations = $this->app->basePath('addons/' . $manifest['id'] . '/migrations');
            if (is_dir($migrations)) {
                $this->loadMigrationsFrom($migrations);
            }

            // Boot the addon's own service provider — its routes/views/bindings.
            $provider = $manifest['provider'] ?? null;
            if (!empty($provider) && class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }
}
