<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Valuations;

use Illuminate\Support\ServiceProvider;

final class ValuationsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register(): void
    {
        $this->app->singleton(Application\CreateValuation::class);
        $this->app->singleton(Application\UpdateValuation::class);
        $this->app->singleton(Application\DeleteValuation::class);
        $this->app->singleton(Application\ScheduleValuation::class);
        $this->app->singleton(Application\CompleteValuation::class);
        $this->app->singleton(Application\ConvertValuation::class);
        $this->app->singleton(Application\CalculateComparables::class);
        $this->app->singleton(Application\CalculateHomeValuation::class);
        $this->app->singleton(Application\GeneratePropertyValuation::class);
        $this->app->singleton(Application\CalculateMortgage::class);
        $this->app->singleton(Application\CalculateRentalYield::class);
    }
}
