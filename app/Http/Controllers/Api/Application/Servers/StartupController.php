<?php

namespace Ruff\Http\Controllers\Api\Application\Servers;

use Ruff\Models\User;
use Ruff\Models\Server;
use Ruff\Services\Servers\StartupModificationService;
use Ruff\Transformers\Api\Application\ServerTransformer;
use Ruff\Http\Controllers\Api\Application\ApplicationApiController;
use Ruff\Http\Requests\Api\Application\Servers\UpdateServerStartupRequest;

class StartupController extends ApplicationApiController
{
    /**
     * StartupController constructor.
     */
    public function __construct(private StartupModificationService $modificationService)
    {
        parent::__construct();
    }

    /**
     * Update the startup and environment settings for a specific server.
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Ruff\Exceptions\Http\Connection\DaemonConnectionException
     * @throws \Ruff\Exceptions\Model\DataValidationException
     * @throws \Ruff\Exceptions\Repository\RecordNotFoundException
     */
    public function index(UpdateServerStartupRequest $request, Server $server): array
    {
        $server = $this->modificationService
            ->setUserLevel(User::USER_LEVEL_ADMIN)
            ->handle($server, $request->validated());

        return $this->fractal->item($server)
            ->transformWith($this->getTransformer(ServerTransformer::class))
            ->toArray();
    }
}
