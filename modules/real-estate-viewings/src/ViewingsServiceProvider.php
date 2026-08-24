<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Viewings;

use Illuminate\Support\ServiceProvider;

final class ViewingsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register(): void
    {
        $this->app->singleton(Application\CreateViewing::class);
        $this->app->singleton(Application\UpdateViewing::class);
        $this->app->singleton(Application\DeleteViewing::class);
        $this->app->singleton(Application\ConfirmViewing::class);
        $this->app->singleton(Application\CompleteViewing::class);
        $this->app->singleton(Application\CancelViewing::class);
        $this->app->singleton(Application\MarkViewingNoShow::class);
    }
}
