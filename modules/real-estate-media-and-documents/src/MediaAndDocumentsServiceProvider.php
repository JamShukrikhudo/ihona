<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocuments;

use Illuminate\Support\ServiceProvider;

final class MediaAndDocumentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Application\GeneratePropertyBrochure::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
