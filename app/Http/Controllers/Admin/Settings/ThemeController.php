<?php

namespace Ruff\Http\Controllers\Admin\Settings;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Illuminate\View\Factory as ViewFactory;
use Ruff\Http\Controllers\Controller;
use Ruff\Contracts\Repository\SettingsRepositoryInterface;
use Ruff\Http\Requests\Admin\Settings\ThemeSettingsFormRequest;

class ThemeController extends Controller
{
    public function __construct(
        private AlertsMessageBag $alert,
        private SettingsRepositoryInterface $settings,
        private ViewFactory $view,
    ) {
    }

    /**
     * Render the appearance / theme editor for the user-facing dashboard.
     */
    public function index(): View
    {
        return $this->view->make('admin.settings.theme', [
            'theme' => $this->currentTheme(),
            'presets' => config('theme.presets', []),
            'labels' => config('theme.labels', []),
        ]);
    }

    /**
     * Persist the theme configuration as a single JSON blob.
     */
    public function update(ThemeSettingsFormRequest $request): RedirectResponse
    {
        $theme = [
            'default_mode' => $request->input('default_mode', 'dark'),
            'allow_toggle' => $request->boolean('allow_toggle'),
            'share_accent_with_admin' => $request->boolean('share_accent'),
            'geometry' => $request->input('geometry', []),
            'modes' => [
                'light' => $request->input('light', []),
                'dark' => $request->input('dark', []),
            ],
        ];

        $this->settings->set('settings::theme', json_encode($theme));
        $this->alert->success('Theme settings have been updated. Reload the client dashboard to see the changes.')->flash();

        return redirect()->route('admin.settings.theme');
    }

    /**
     * Stored theme merged over the packaged defaults so the editor always has
     * a complete set of tokens to render.
     */
    private function currentTheme(): array
    {
        $defaults = config('theme');
        unset($defaults['presets'], $defaults['labels']);

        $stored = json_decode((string) $this->settings->get('settings::theme', ''), true);

        return is_array($stored) ? array_replace_recursive($defaults, $stored) : $defaults;
    }
}
