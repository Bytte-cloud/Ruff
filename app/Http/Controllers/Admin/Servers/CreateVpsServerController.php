<?php

namespace Ruff\Http\Controllers\Admin\Servers;

use Ruff\Models\Pup;
use Ruff\Models\Node;
use Illuminate\View\View;
use Ruff\Models\Location;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Ruff\Http\Controllers\Controller;
use Illuminate\View\Factory as ViewFactory;
use Ruff\Repositories\Eloquent\NodeRepository;
use Ruff\Services\Servers\ServerCreationService;
use Ruff\Http\Requests\Admin\VpsServerFormRequest;

class CreateVpsServerController extends Controller
{
    /**
     * CreateVpsServerController constructor.
     */
    public function __construct(
        private AlertsMessageBag $alert,
        private NodeRepository $nodeRepository,
        private ServerCreationService $creationService,
        private ViewFactory $view,
    ) {
    }

    /**
     * Displays the create VPS server page.
     */
    public function index(): View|RedirectResponse
    {
        $nodes = Node::all();
        if (count($nodes) < 1) {
            $this->alert->warning(trans('admin/server.alerts.node_required'))->flash();

            return redirect()->route('admin.nodes');
        }

        $pups = Pup::all();
        if (count($pups) < 1) {
            $this->alert->warning('You must create at least one VPS template (Pup) before creating a VPS.')->flash();

            return redirect()->route('admin.pups');
        }

        \JavaScript::put([
            'nodeData' => $this->nodeRepository->getNodesForServerCreation(),
        ]);

        return $this->view->make('admin.servers.new-vps', [
            'locations' => Location::all(),
            'pups' => $pups,
        ]);
    }

    /**
     * Create a new VPS server on the remote system. The execution backend is
     * forced to "qemu" and the base image / OS / firmware are sourced from the
     * selected Pup rather than from user input.
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Ruff\Exceptions\DisplayException
     * @throws \Ruff\Exceptions\Service\Deployment\NoViableAllocationException
     * @throws \Ruff\Exceptions\Service\Deployment\NoViableNodeException
     * @throws \Throwable
     */
    public function store(VpsServerFormRequest $request): RedirectResponse
    {
        $data = $request->except(['_token']);

        /** @var Pup $pup */
        $pup = Pup::query()->findOrFail($data['pup_id']);

        $data['environment_type'] = 'qemu';
        $data['image'] = $pup->image_url;
        $data['startup'] = '';
        $data['egg_id'] = null;
        $data['nest_id'] = null;
        $data['skip_scripts'] = true;

        $server = $this->creationService->handle($data);

        $this->alert->success(trans('admin/server.alerts.server_created'))->flash();

        return new RedirectResponse('/admin/servers/view/' . $server->id);
    }
}
