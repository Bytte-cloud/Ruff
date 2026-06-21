<?php

namespace Ruff\Http\Controllers\Admin;

use Illuminate\View\View;
use Ruff\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Ruff\Http\Controllers\Controller;
use Illuminate\View\Factory as ViewFactory;
use Ruff\Http\Requests\Admin\AnnouncementFormRequest;

class AnnouncementController extends Controller
{
    public function __construct(
        protected AlertsMessageBag $alert,
        protected ViewFactory $view,
    ) {
    }

    /**
     * Display the announcement overview page.
     */
    public function index(): View
    {
        return $this->view->make('admin.announcements.index', [
            'announcements' => Announcement::query()->orderBy('sort_order')->orderByDesc('id')->get(),
        ]);
    }

    /**
     * Display the announcement edit page.
     */
    public function view(Announcement $announcement): View
    {
        return $this->view->make('admin.announcements.view', ['announcement' => $announcement]);
    }

    /**
     * Handle a request to create a new announcement.
     */
    public function create(AnnouncementFormRequest $request): RedirectResponse
    {
        $announcement = Announcement::create($request->normalize());
        $this->alert->success('Announcement was created successfully.')->flash();

        return redirect()->route('admin.announcements.view', $announcement->id);
    }

    /**
     * Handle a request to update or delete an announcement.
     */
    public function update(AnnouncementFormRequest $request, Announcement $announcement): RedirectResponse
    {
        if ($request->input('action') === 'delete') {
            return $this->delete($announcement);
        }

        $announcement->update($request->normalize());
        $this->alert->success('Announcement was updated successfully.')->flash();

        return redirect()->route('admin.announcements.view', $announcement->id);
    }

    /**
     * Delete an announcement.
     */
    public function delete(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();
        $this->alert->success('Announcement was deleted successfully.')->flash();

        return redirect()->route('admin.announcements');
    }
}
