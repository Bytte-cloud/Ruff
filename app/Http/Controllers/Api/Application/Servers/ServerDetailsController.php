<?php

namespace Ruff\Http\Controllers\Api\Application\Servers;

use Ruff\Models\Server;
use Ruff\Services\Servers\BuildModificationService;
use Ruff\Services\Servers\DetailsModificationService;
use Ruff\Transformers\Api\Application\ServerTransformer;
use Ruff\Http\Controllers\Api\Application\ApplicationApiController;
use Ruff\Http\Requests\Api\Application\Servers\UpdateServerDetailsRequest;
use Ruff\Http\Requests\Api\Application\Servers\UpdateServerBuildConfigurationRequest;

class ServerDetailsController extends ApplicationApiController
{
    /**
     * ServerDetailsController constructor.
     */
    public function __construct(
        private BuildModificationService $buildModificationService,
        private DetailsModificationService $detailsModificationService,
    ) {
        parent::__construct();
    }

    /**
     * Update the details for a specific server.
     *
     * @throws \Ruff\Exceptions\DisplayException
     * @throws \Ruff\Exceptions\Model\DataValidationException
     * @throws \Ruff\Exceptions\Repository\RecordNotFoundException
     */
    public function details(UpdateServerDetailsRequest $request, Server $server): array
    {
        $updated = $this->detailsModificationService->returnUpdatedModel()->handle(
            $server,
            $request->validated()
        );

        return $this->fractal->item($updated)
            ->transformWith($this->getTransformer(ServerTransformer::class))
            ->toArray();
    }

    /**
     * Update the build details for a specific server.
     *
     * @throws \Ruff\Exceptions\DisplayException
     * @throws \Ruff\Exceptions\Model\DataValidationException
     * @throws \Ruff\Exceptions\Repository\RecordNotFoundException
     */
    public function build(UpdateServerBuildConfigurationRequest $request, Server $server): array
    {
        $server = $this->buildModificationService->handle($server, $request->validated());

        return $this->fractal->item($server)
            ->transformWith($this->getTransformer(ServerTransformer::class))
            ->toArray();
    }
}
