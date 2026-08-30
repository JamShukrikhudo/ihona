<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Matching;

use Illuminate\Support\ServiceProvider;

final class MatchingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register(): void
    {
        $this->app->singleton(Application\CalculateMatchScore::class);
        $this->app->singleton(Application\RankPropertyRecommendations::class);
    }
}
