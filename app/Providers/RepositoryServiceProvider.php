<?php

namespace Ruff\Providers;

use Illuminate\Support\ServiceProvider;
use Ruff\Repositories\Eloquent\EggRepository;
use Ruff\Repositories\Eloquent\NestRepository;
use Ruff\Repositories\Eloquent\NodeRepository;
use Ruff\Repositories\Eloquent\TaskRepository;
use Ruff\Repositories\Eloquent\UserRepository;
use Ruff\Repositories\Eloquent\ApiKeyRepository;
use Ruff\Repositories\Eloquent\ServerRepository;
use Ruff\Repositories\Eloquent\SessionRepository;
use Ruff\Repositories\Eloquent\SubuserRepository;
use Ruff\Repositories\Eloquent\DatabaseRepository;
use Ruff\Repositories\Eloquent\LocationRepository;
use Ruff\Repositories\Eloquent\ScheduleRepository;
use Ruff\Repositories\Eloquent\SettingsRepository;
use Ruff\Repositories\Eloquent\AllocationRepository;
use Ruff\Contracts\Repository\EggRepositoryInterface;
use Ruff\Repositories\Eloquent\EggVariableRepository;
use Ruff\Contracts\Repository\NestRepositoryInterface;
use Ruff\Contracts\Repository\NodeRepositoryInterface;
use Ruff\Contracts\Repository\TaskRepositoryInterface;
use Ruff\Contracts\Repository\UserRepositoryInterface;
use Ruff\Repositories\Eloquent\DatabaseHostRepository;
use Ruff\Contracts\Repository\ApiKeyRepositoryInterface;
use Ruff\Contracts\Repository\ServerRepositoryInterface;
use Ruff\Repositories\Eloquent\ServerVariableRepository;
use Ruff\Contracts\Repository\SessionRepositoryInterface;
use Ruff\Contracts\Repository\SubuserRepositoryInterface;
use Ruff\Contracts\Repository\DatabaseRepositoryInterface;
use Ruff\Contracts\Repository\LocationRepositoryInterface;
use Ruff\Contracts\Repository\ScheduleRepositoryInterface;
use Ruff\Contracts\Repository\SettingsRepositoryInterface;
use Ruff\Contracts\Repository\AllocationRepositoryInterface;
use Ruff\Contracts\Repository\EggVariableRepositoryInterface;
use Ruff\Contracts\Repository\DatabaseHostRepositoryInterface;
use Ruff\Contracts\Repository\ServerVariableRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register all the repository bindings.
     */
    public function register(): void
    {
        // Eloquent Repositories
        $this->app->bind(AllocationRepositoryInterface::class, AllocationRepository::class);
        $this->app->bind(ApiKeyRepositoryInterface::class, ApiKeyRepository::class);
        $this->app->bind(DatabaseRepositoryInterface::class, DatabaseRepository::class);
        $this->app->bind(DatabaseHostRepositoryInterface::class, DatabaseHostRepository::class);
        $this->app->bind(EggRepositoryInterface::class, EggRepository::class);
        $this->app->bind(EggVariableRepositoryInterface::class, EggVariableRepository::class);
        $this->app->bind(LocationRepositoryInterface::class, LocationRepository::class);
        $this->app->bind(NestRepositoryInterface::class, NestRepository::class);
        $this->app->bind(NodeRepositoryInterface::class, NodeRepository::class);
        $this->app->bind(ScheduleRepositoryInterface::class, ScheduleRepository::class);
        $this->app->bind(ServerRepositoryInterface::class, ServerRepository::class);
        $this->app->bind(ServerVariableRepositoryInterface::class, ServerVariableRepository::class);
        $this->app->bind(SessionRepositoryInterface::class, SessionRepository::class);
        $this->app->bind(SettingsRepositoryInterface::class, SettingsRepository::class);
        $this->app->bind(SubuserRepositoryInterface::class, SubuserRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }
}
