<?php

namespace Ruff\Http\ViewComposers;

use Illuminate\View\View;
use Ruff\Services\Helpers\AssetHashService;
use Ruff\Contracts\Repository\SettingsRepositoryInterface;

class AssetComposer
{
    /**
     * AssetComposer constructor.
     */
    public function __construct(
        private AssetHashService $assetHashService,
        private SettingsRepositoryInterface $settings,
    ) {
    }

    /**
     * Provide access to the asset service in the views.
     */
    public function compose(View $view): void
    {
        $view->with('asset', $this->assetHashService);
        $view->with('siteConfiguration', [
            'name' => config('app.name') ?? 'Ruff',
            'locale' => config('app.locale') ?? 'en',
            'recaptcha' => [
                'enabled' => config('recaptcha.enabled', false),
                'siteKey' => config('recaptcha.website_key') ?? '',
            ],
            'theme' => $this->theme(),
        ]);
    }

    /**
     * Resolve the client theme: packaged defaults overridden by anything saved
     * in the admin Theme tab. Falls back to defaults if settings are unreadable
     * (e.g. during initial installation before migrations have run).
     */
    private function theme(): array
    {
        $defaults = config('theme');
        unset($defaults['presets'], $defaults['labels']);

        try {
            $stored = json_decode((string) $this->settings->get('settings::theme', ''), true);
        } catch (\Throwable $e) {
            $stored = null;
        }

        return is_array($stored) ? array_replace_recursive($defaults, $stored) : $defaults;
    }
}
