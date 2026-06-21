<?php

namespace Ruff\Http\Controllers\Admin\Servers;

use Ruff\Models\Pup;
use Ruff\Models\Node;
use Illuminate\View\View;
use Ruff\Models\Location;
use Illuminate\Support\Str;
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

        // Assemble the cloud-init provisioning inputs the daemon needs. The daemon
        // refuses to build a VM without a login, so if neither a password nor SSH
        // keys were supplied we generate a password and surface it to the admin.
        $vmOptions = array_filter([
            'VM_USER' => $this->cleanInput($data['vm_user'] ?? null),
            'VM_SSH_KEYS' => $this->cleanInput($data['vm_ssh_keys'] ?? null),
            'VM_HOSTNAME' => $this->cleanInput($data['vm_hostname'] ?? null),
        ], fn ($v) => !is_null($v));

        $password = $this->cleanInput($data['vm_password'] ?? null);
        $generatedPassword = null;
        if (is_null($password) && !isset($vmOptions['VM_SSH_KEYS'])) {
            $password = Str::password(16, true, true, false, false);
            $generatedPassword = $password;
        }
        if (!is_null($password)) {
            $vmOptions['VM_PASSWORD'] = $password;
        }

        $data['vm_options'] = $vmOptions;
        unset($data['vm_user'], $data['vm_password'], $data['vm_ssh_keys'], $data['vm_hostname']);

        $server = $this->creationService->handle($data);

        if (!is_null($generatedPassword)) {
            $this->alert->success(
                'VPS created. Generated <strong>' . e($vmOptions['VM_USER'] ?? 'root') . '</strong> password: <code>'
                . e($generatedPassword) . '</code> — save it now, it is not shown again.'
            )->flash();
        } else {
            $this->alert->success(trans('admin/server.alerts.server_created'))->flash();
        }

        return new RedirectResponse('/admin/servers/view/' . $server->id);
    }

    /**
     * Trim a free-text input, returning null when it is empty.
     */
    private function cleanInput(?string $value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
