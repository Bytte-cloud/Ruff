<?php

namespace Ruff\Services\Addons;

use Ruff\Models\Addon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Foundation\Application;

/**
 * Discovers and manages drop-in addons.
 *
 * An addon is a self-contained directory under the project `addons/` folder:
 *
 *   addons/<id>/
 *     addon.json                 manifest (see the README in that directory)
 *     src/                        PSR-4 autoloaded classes (incl. the provider)
 *     routes/  migrations/  ...   loaded by the addon's own service provider
 *
 * Enabling an addon runs its migrations and registers its service provider on
 * every boot (see AddonServiceProvider); disabling rolls its migrations back, so
 * the operation is fully reversible.
 */
class AddonManager
{
    public function __construct(
        private Application $app,
        private Filesystem $files,
    ) {
    }

    /**
     * Absolute path to the addons directory.
     */
    public function basePath(): string
    {
        return $this->app->basePath('addons');
    }

    /**
     * Path of the migrations directory relative to the project root, as Artisan's
     * `--path` option expects it.
     */
    private function relativeMigrationsPath(string $id): string
    {
        return 'addons/' . $id . '/migrations';
    }

    private function migrationsPath(string $id): string
    {
        return $this->basePath() . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . 'migrations';
    }

    /**
     * Parse every addon.json on disk, keyed by addon id. Manifests missing an id
     * or with invalid JSON are skipped rather than fataling the whole panel.
     */
    public function manifests(): array
    {
        $manifests = [];
        if (!$this->files->isDirectory($this->basePath())) {
            return $manifests;
        }

        foreach ($this->files->directories($this->basePath()) as $directory) {
            $file = $directory . DIRECTORY_SEPARATOR . 'addon.json';
            if (!$this->files->exists($file)) {
                continue;
            }

            $data = json_decode($this->files->get($file), true);
            if (!is_array($data) || empty($data['id'])) {
                continue;
            }

            $data['id'] = (string) $data['id'];
            $manifests[$data['id']] = $data;
        }

        return $manifests;
    }

    public function manifest(string $id): ?array
    {
        return $this->manifests()[$id] ?? null;
    }

    /**
     * Ids of enabled addons. Returns an empty list if the addons table does not
     * exist yet (e.g. before this feature's migration has run), so this is safe
     * to call from a service provider at boot.
     */
    public function enabledIds(): array
    {
        try {
            return Addon::query()->where('enabled', true)->pluck('addon_id')->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function isEnabled(string $id): bool
    {
        return in_array($id, $this->enabledIds(), true);
    }

    /**
     * Manifests of the currently enabled addons.
     */
    public function enabledManifests(): array
    {
        $enabled = $this->enabledIds();

        return array_values(array_filter(
            $this->manifests(),
            fn ($manifest) => in_array($manifest['id'], $enabled, true)
        ));
    }

    /**
     * Discovered addons merged with their enabled state, for the admin UI.
     */
    public function all(): array
    {
        $enabled = $this->enabledIds();

        return array_map(function (array $manifest) use ($enabled) {
            return [
                'id' => $manifest['id'],
                'name' => $manifest['name'] ?? $manifest['id'],
                'version' => $manifest['version'] ?? '—',
                'description' => $manifest['description'] ?? '',
                'author' => $manifest['author'] ?? '',
                'enabled' => in_array($manifest['id'], $enabled, true),
                'has_migrations' => $this->files->isDirectory($this->migrationsPath($manifest['id'])),
            ];
        }, array_values($this->manifests()));
    }

    /**
     * Enable an addon: run its migrations (if any) then record it as enabled.
     *
     * @throws \RuntimeException when the addon is not present on disk.
     */
    public function enable(string $id): void
    {
        $manifest = $this->manifest($id);
        if (is_null($manifest)) {
            throw new \RuntimeException("Addon [{$id}] was not found on disk.");
        }

        if ($this->files->isDirectory($this->migrationsPath($id))) {
            Artisan::call('migrate', [
                '--path' => $this->relativeMigrationsPath($id),
                '--force' => true,
            ]);
        }

        Addon::query()->updateOrCreate(
            ['addon_id' => $id],
            ['enabled' => true, 'version' => $manifest['version'] ?? null],
        );
    }

    /**
     * Disable an addon: roll its migrations back (reversible) then record it as
     * disabled. `migrate:reset --path` only touches migrations whose files live
     * under the addon, leaving the rest of the schema untouched.
     */
    public function disable(string $id): void
    {
        if ($this->files->isDirectory($this->migrationsPath($id))) {
            Artisan::call('migrate:reset', [
                '--path' => $this->relativeMigrationsPath($id),
                '--force' => true,
            ]);
        }

        Addon::query()->where('addon_id', $id)->update(['enabled' => false]);
    }
}
