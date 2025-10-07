<?php

namespace Ruff\Http\Controllers\Admin;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Ruff\Models\Node;
use Illuminate\Http\Response;
use Ruff\Models\Allocation;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Illuminate\View\Factory as ViewFactory;
use Ruff\Http\Controllers\Controller;
use Ruff\Services\Nodes\NodeUpdateService;
use Illuminate\Cache\Repository as CacheRepository;
use Ruff\Services\Nodes\NodeCreationService;
use Ruff\Services\Nodes\NodeDeletionService;
use Ruff\Services\Allocations\AssignmentService;
use Ruff\Services\Helpers\SoftwareVersionService;
use Ruff\Http\Requests\Admin\Node\NodeFormRequest;
use Ruff\Contracts\Repository\NodeRepositoryInterface;
use Ruff\Contracts\Repository\ServerRepositoryInterface;
use Ruff\Http\Requests\Admin\Node\AllocationFormRequest;
use Ruff\Services\Allocations\AllocationDeletionService;
use Ruff\Contracts\Repository\LocationRepositoryInterface;
use Ruff\Contracts\Repository\AllocationRepositoryInterface;
use Ruff\Http\Requests\Admin\Node\AllocationAliasFormRequest;

class NodesController extends Controller
{
    /**
     * NodesController constructor.
     */
    public function __construct(
        protected AlertsMessageBag $alert,
        protected AllocationDeletionService $allocationDeletionService,
        protected AllocationRepositoryInterface $allocationRepository,
        protected AssignmentService $assignmentService,
        protected CacheRepository $cache,
        protected NodeCreationService $creationService,
        protected NodeDeletionService $deletionService,
        protected LocationRepositoryInterface $locationRepository,
        protected NodeRepositoryInterface $repository,
        protected ServerRepositoryInterface $serverRepository,
        protected NodeUpdateService $updateService,
        protected SoftwareVersionService $versionService,
        protected ViewFactory $view,
    ) {
    }

    /**
     * Displays create new node page.
     */
    public function create(): View|RedirectResponse
    {
        $locations = $this->locationRepository->all();
        if (count($locations) < 1) {
            $this->alert->warning(trans('admin/node.notices.location_required'))->flash();

            return redirect()->route('admin.locations');
        }

        return $this->view->make('admin.nodes.new', ['locations' => $locations]);
    }

    /**
     * Post controller to create a new node on the system.
     *
     * @throws \Ruff\Exceptions\Model\DataValidationException
     */
    public function store(NodeFormRequest $request): RedirectResponse
    {
        $node = $this->creationService->handle($request->normalize());
        $this->alert->info(trans('admin/node.notices.node_created'))->flash();

        return redirect()->route('admin.nodes.view.allocation', $node->id);
    }

    /**
     * Updates settings for a node.
     *
     * @throws \Ruff\Exceptions\DisplayException
     * @throws \Ruff\Exceptions\Model\DataValidationException
     * @throws \Ruff\Exceptions\Repository\RecordNotFoundException
     */
    public function updateSettings(NodeFormRequest $request, Node $node): RedirectResponse
    {
        $this->updateService->handle($node, $request->normalize(), $request->input('reset_secret') === 'on');
        $this->alert->success(trans('admin/node.notices.node_updated'))->flash();

        return redirect()->route('admin.nodes.view.settings', $node->id)->withInput();
    }

    /**
     * Removes a single allocation from a node.
     *
     * @throws \Ruff\Exceptions\Service\Allocation\ServerUsingAllocationException
     */
    public function allocationRemoveSingle(int $node, Allocation $allocation): Response
    {
        $this->allocationDeletionService->handle($allocation);

        return response('', 204);
    }

    /**
     * Removes multiple individual allocations from a node.
     *
     * @throws \Ruff\Exceptions\Service\Allocation\ServerUsingAllocationException
     */
    public function allocationRemoveMultiple(Request $request, int $node): Response
    {
        $allocations = $request->input('allocations');
        foreach ($allocations as $rawAllocation) {
            $allocation = new Allocation();
            $allocation->id = $rawAllocation['id'];
            $this->allocationRemoveSingle($node, $allocation);
        }

        return response('', 204);
    }

    /**
     * Remove all allocations for a specific IP at once on a node.
     */
    public function allocationRemoveBlock(Request $request, int $node): RedirectResponse
    {
        $this->allocationRepository->deleteWhere([
            ['node_id', '=', $node],
            ['server_id', '=', null],
            ['ip', '=', $request->input('ip')],
        ]);

        $this->alert->success(trans('admin/node.notices.unallocated_deleted', ['ip' => htmlspecialchars($request->input('ip'))]))
            ->flash();

        return redirect()->route('admin.nodes.view.allocation', $node);
    }

    /**
     * Sets an alias for a specific allocation on a node.
     *
     * @throws \Ruff\Exceptions\Model\DataValidationException
     * @throws \Ruff\Exceptions\Repository\RecordNotFoundException
     */
    public function allocationSetAlias(AllocationAliasFormRequest $request): \Symfony\Component\HttpFoundation\Response
    {
        $this->allocationRepository->update($request->input('allocation_id'), [
            'ip_alias' => (empty($request->input('alias'))) ? null : $request->input('alias'),
        ]);

        return response('', 204);
    }

    /**
     * Creates new allocations on a node.
     *
     * @throws \Ruff\Exceptions\Service\Allocation\CidrOutOfRangeException
     * @throws \Ruff\Exceptions\Service\Allocation\InvalidPortMappingException
     * @throws \Ruff\Exceptions\Service\Allocation\PortOutOfRangeException
     * @throws \Ruff\Exceptions\Service\Allocation\TooManyPortsInRangeException
     */
    public function createAllocation(AllocationFormRequest $request, Node $node): RedirectResponse
    {
        $this->assignmentService->handle($node, $request->normalize());
        $this->alert->success(trans('admin/node.notices.allocations_added'))->flash();

        return redirect()->route('admin.nodes.view.allocation', $node->id);
    }

    /**
     * Deletes a node from the system.
     *
     * @throws \Ruff\Exceptions\DisplayException
     */
    public function delete(int|Node $node): RedirectResponse
    {
        $this->deletionService->handle($node);
        $this->alert->success(trans('admin/node.notices.node_deleted'))->flash();

        return redirect()->route('admin.nodes');
    }
}
