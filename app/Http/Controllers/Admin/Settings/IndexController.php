<?php

namespace Ruff\Http\Controllers\Admin\Settings;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\View\Factory as ViewFactory;
use Ruff\Http\Controllers\Controller;
use Ruff\Traits\Helpers\AvailableLanguages;
use Ruff\Services\Helpers\SoftwareVersionService;
use Ruff\Contracts\Repository\SettingsRepositoryInterface;
use Ruff\Http\Requests\Admin\Settings\BaseSettingsFormRequest;

class IndexController extends Controller
{
    use AvailableLanguages;

    /**
     * IndexController constructor.
     */
    public function __construct(
        private AlertsMessageBag $alert,
        private Kernel $kernel,
        private SettingsRepositoryInterface $settings,
        private SoftwareVersionService $versionService,
        private ViewFactory $view,
    ) {
    }

    /**
     * Render the UI for basic Panel settings.
     */
    public function index(): View
    {
        return $this->view->make('admin.settings.index', [
            'version' => $this->versionService,
            'languages' => $this->getAvailableLanguages(true),
        ]);
    }

    /**
     * Handle settings update.
     *
     * @throws \Ruff\Exceptions\Model\DataValidationException
     * @throws \Ruff\Exceptions\Repository\RecordNotFoundException
     */
    public function update(BaseSettingsFormRequest $request): RedirectResponse
    {
        foreach ($request->normalize() as $key => $value) {
            $this->settings->set('settings::' . $key, $value);
        }

        $this->kernel->call('queue:restart');
        $this->alert->success('Panel settings have been updated successfully and the queue worker was restarted to apply these changes.')->flash();

        return redirect()->route('admin.settings');
    }
}
