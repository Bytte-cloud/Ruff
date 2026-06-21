<?php

namespace Ruff\Http\Controllers\Admin;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Ruff\Http\Controllers\Controller;
use Ruff\Services\Addons\AddonManager;
use Illuminate\View\Factory as ViewFactory;

class AddonController extends Controller
{
    public function __construct(
        protected AlertsMessageBag $alert,
        protected AddonManager $manager,
        protected ViewFactory $view,
    ) {
    }

    /**
     * List the addons discovered under the project addons/ directory.
     */
    public function index(): View
    {
        return $this->view->make('admin.addons.index', [
            'addons' => $this->manager->all(),
            'path' => $this->manager->basePath(),
        ]);
    }

    /**
     * Enable an addon (runs its migrations and registers it on every boot).
     */
    public function enable(string $addon): RedirectResponse
    {
        try {
            $this->manager->enable($addon);
            $this->alert->success('Addon "' . e($addon) . '" was enabled.')->flash();
        } catch (\Throwable $exception) {
            $this->alert->danger('Failed to enable addon: ' . e($exception->getMessage()))->flash();
        }

        return redirect()->route('admin.addons');
    }

    /**
     * Disable an addon (rolls its migrations back — reversible).
     */
    public function disable(string $addon): RedirectResponse
    {
        try {
            $this->manager->disable($addon);
            $this->alert->success('Addon "' . e($addon) . '" was disabled.')->flash();
        } catch (\Throwable $exception) {
            $this->alert->danger('Failed to disable addon: ' . e($exception->getMessage()))->flash();
        }

        return redirect()->route('admin.addons');
    }
}
