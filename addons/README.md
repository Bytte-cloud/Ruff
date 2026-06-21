# Ruff Addons

Addons are self-contained, drop-in extensions for the panel. Drop an addon's
directory into this folder, then enable it from **Admin → Addons**. Enabling runs
its migrations and registers its routes/services; disabling rolls the migrations
back, so addons are fully reversible.

An addon is essentially a small Laravel package that lives in `addons/<id>/`
instead of `vendor/`. You write a normal service provider; the panel autoloads
your classes and boots your provider when the addon is enabled.

## Directory layout

```
addons/
  my-addon/
    addon.json                         # manifest (required)
    src/                               # PSR-4 autoloaded classes
      MyAddonServiceProvider.php       # your service provider
      Http/Controllers/...             # controllers, etc.
    routes/
      web.php                          # routes you load from your provider
    migrations/
      2026_01_01_000000_create_my_table.php
```

## Manifest — `addon.json`

```json
{
  "id": "my-addon",
  "name": "My Addon",
  "version": "1.0.0",
  "description": "What this addon does.",
  "author": "You",
  "namespace": "Addons\\MyAddon\\",
  "provider": "Addons\\MyAddon\\MyAddonServiceProvider"
}
```

| Field | Required | Meaning |
|---|---|---|
| `id` | yes | Unique slug; **must match the directory name**. |
| `namespace` | yes | PSR-4 prefix mapped to `addons/<id>/src/`. |
| `provider` | yes | Fully-qualified service provider class (under `namespace`). |
| `name`, `version`, `description`, `author` | no | Shown in the admin UI. |

## Service provider

A standard Laravel `ServiceProvider`. Register routes, views, bindings, console
commands — anything a package can. It is only booted while the addon is enabled.

```php
namespace Addons\MyAddon;

use Illuminate\Support\ServiceProvider;

class MyAddonServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        // $this->loadViewsFrom(__DIR__ . '/../views', 'my-addon');
    }
}
```

## Migrations

Put migrations in `addons/<id>/migrations/`. They are **not** run by the panel's
normal `php artisan migrate` unless the addon is enabled. Enabling the addon runs
them (`migrate --path=addons/<id>/migrations`); disabling rolls them back
(`migrate:reset --path=...`) — only the addon's own migrations are affected.

Write reversible migrations (a real `down()`), since disabling relies on it.

## Lifecycle

1. Drop the addon directory into `addons/`.
2. **Admin → Addons** lists it (discovered from `addon.json`).
3. **Enable** → migrations run, provider boots on every request.
4. **Disable** → migrations roll back, provider no longer boots.

See `addons/hello-world/` for a complete minimal example.

> Distribution (uploading/installing addons from a registry or a zip through the
> UI) builds on this foundation and is intentionally not included yet — today an
> addon is published by sharing its directory.
