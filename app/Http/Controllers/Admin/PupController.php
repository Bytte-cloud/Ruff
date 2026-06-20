<?php

namespace Ruff\Http\Controllers\Admin;

use Ruff\Models\Pup;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Ruff\Http\Controllers\Controller;
use Illuminate\View\Factory as ViewFactory;
use Ruff\Http\Requests\Admin\PupFormRequest;

class PupController extends Controller
{
    /**
     * PupController constructor.
     */
    public function __construct(
        protected AlertsMessageBag $alert,
        protected ViewFactory $view,
    ) {
    }

    /**
     * Return the Pup (VPS template) overview page.
     */
    public function index(): View
    {
        return $this->view->make('admin.pups.index', [
            'pups' => Pup::query()->withCount('servers')->get(),
        ]);
    }

    /**
     * Return the Pup view/edit page.
     */
    public function view(Pup $pup): View
    {
        return $this->view->make('admin.pups.view', [
            'pup' => $pup->loadCount('servers'),
        ]);
    }

    /**
     * Handle a request to create a new Pup.
     */
    public function create(PupFormRequest $request): RedirectResponse
    {
        $pup = Pup::create($request->normalize());
        $this->alert->success('VPS template was created successfully.')->flash();

        return redirect()->route('admin.pups.view', $pup->id);
    }

    /**
     * Handle a request to update or delete a Pup.
     */
    public function update(PupFormRequest $request, Pup $pup): RedirectResponse
    {
        if ($request->input('action') === 'delete') {
            return $this->delete($pup);
        }

        $pup->update($request->normalize());
        $this->alert->success('VPS template was updated successfully.')->flash();

        return redirect()->route('admin.pups.view', $pup->id);
    }

    /**
     * Delete a Pup from the system, refusing while any servers still reference it.
     */
    public function delete(Pup $pup): RedirectResponse
    {
        if ($pup->servers()->count() > 0) {
            $this->alert->danger('Cannot delete a VPS template that still has servers using it.')->flash();

            return redirect()->route('admin.pups.view', $pup->id);
        }

        $pup->delete();
        $this->alert->success('VPS template was deleted successfully.')->flash();

        return redirect()->route('admin.pups');
    }
}
