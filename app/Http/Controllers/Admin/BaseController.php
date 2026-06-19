<?php

namespace Ruff\Http\Controllers\Admin;

use Ruff\Models\Nest;
use Ruff\Models\Node;
use Ruff\Models\User;
use Ruff\Models\Server;
use Illuminate\View\View;
use Ruff\Models\Location;
use Ruff\Http\Controllers\Controller;
use Illuminate\View\Factory as ViewFactory;
use Ruff\Services\Helpers\SoftwareVersionService;

class BaseController extends Controller
{
    /**
     * BaseController constructor.
     */
    public function __construct(private SoftwareVersionService $version, private ViewFactory $view)
    {
    }

    /**
     * Return the admin index view.
     */
    public function index(): View
    {
        return $this->view->make('admin.index', [
            'version' => $this->version,
            'stats' => [
                'users' => User::query()->count(),
                'servers' => Server::query()->count(),
                'nodes' => Node::query()->count(),
                'locations' => Location::query()->count(),
                'nests' => Nest::query()->count(),
            ],
        ]);
    }
}
