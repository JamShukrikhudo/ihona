<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core;

use Illuminate\Support\ServiceProvider;

final class RealEstateCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Application\CreateBranch::class);
        $this->app->singleton(Application\UpdateBranch::class);
        $this->app->singleton(Application\DeleteBranch::class);
        $this->app->singleton(Application\CreateAgency::class);
        $this->app->singleton(Application\UpdateAgency::class);
        $this->app->singleton(Application\DeleteAgency::class);
        $this->app->singleton(Application\CreateTerritory::class);
        $this->app->singleton(Application\UpdateTerritory::class);
        $this->app->singleton(Application\DeleteTerritory::class);
        $this->app->singleton(Application\NextNumber::class);
        $this->app->singleton(Application\SetTerminology::class);
        $this->app->singleton(Application\DefineStatus::class);
        $this->app->singleton(Application\RecordAuditEntry::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
