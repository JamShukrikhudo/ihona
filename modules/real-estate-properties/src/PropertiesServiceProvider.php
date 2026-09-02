<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties;

use Illuminate\Support\ServiceProvider;

final class PropertiesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Application\CreateProperty::class);
        $this->app->singleton(Application\UpsertPropertyUnit::class);
        $this->app->singleton(Application\RecordPropertyKey::class);
        $this->app->singleton(Application\TogglePropertyFavorite::class);
        $this->app->singleton(Application\RemovePropertyFavorite::class);
        $this->app->singleton(Application\SavePropertySearch::class);
        $this->app->singleton(Application\DeletePropertySearch::class);
        $this->app->singleton(Application\SubmitPropertyReview::class);
        $this->app->singleton(Application\SubmitNeighborhoodReview::class);
        $this->app->singleton(Application\CreatePriceAlert::class);
        $this->app->singleton(Application\UpdatePriceAlert::class);
        $this->app->singleton(Application\DeletePriceAlert::class);
        $this->app->singleton(Application\TogglePriceAlert::class);
        $this->app->singleton(Application\CheckPriceAlerts::class);
        $this->app->singleton(Application\ConfigurePropertyArTour::class);
        $this->app->singleton(Application\FetchWalkabilityScores::class);
        $this->app->singleton(Application\GeneratePropertyQrCode::class);
        $this->app->singleton(Application\SendPropertyToFriend::class);
        $this->app->singleton(Application\GeneratePropertyDescription::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
