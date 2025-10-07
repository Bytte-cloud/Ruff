<?php

namespace Ruff\Services\Locations;

use Ruff\Models\Location;
use Ruff\Contracts\Repository\LocationRepositoryInterface;

class LocationCreationService
{
    /**
     * LocationCreationService constructor.
     */
    public function __construct(protected LocationRepositoryInterface $repository)
    {
    }

    /**
     * Create a new location.
     *
     * @throws \Ruff\Exceptions\Model\DataValidationException
     */
    public function handle(array $data): Location
    {
        return $this->repository->create($data);
    }
}
