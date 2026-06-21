<?php

namespace Ruff\Http\Controllers\Admin\Servers;

use Ruff\Models\Server;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Ruff\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Ruff\Models\Filters\AdminServerFilter;
use Illuminate\Contracts\View\Factory as ViewFactory;

class ServerController extends Controller
{
    /**
     * ServerController constructor.
     */
    public function __construct(private ViewFactory $view)
    {
    }

    /**
     * Returns all the servers that exist on the system using a paginated result set. If
     * a query is passed along in the request it is also passed to the repository function.
     *
     * The optional "type" query parameter scopes the list to a backend:
     * "docker" (game servers), "qemu" (VPS) or "all" (default).
     */
    public function index(Request $request): View
    {
        $type = $request->query('type', 'all');
        $type = in_array($type, ['docker', 'qemu'], true) ? $type : 'all';

        $base = Server::query()->with('node', 'user', 'allocation');
        if ($type === 'qemu') {
            $base->where('environment_type', 'qemu');
        } elseif ($type === 'docker') {
            // Treat a missing environment_type as a Docker (game) server so servers
            // that predate the column still show up under the game-server tab.
            $base->where(function ($query) {
                $query->where('environment_type', 'docker')->orWhereNull('environment_type');
            });
        }

        $servers = QueryBuilder::for($base)
            ->allowedFilters([
                AllowedFilter::exact('owner_id'),
                AllowedFilter::custom('*', new AdminServerFilter()),
            ])
            ->paginate(config()->get('Ruff.paginate.admin.servers'));

        return $this->view->make('admin.servers.index', [
            'servers' => $servers,
            'type' => $type,
        ]);
    }
}
