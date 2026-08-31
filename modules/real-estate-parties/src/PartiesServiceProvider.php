<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Parties;

use Illuminate\Support\ServiceProvider;

final class PartiesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Application\CreateParty::class);
        $this->app->singleton(Application\UpdateParty::class);
        $this->app->singleton(Application\DeleteParty::class);
        $this->app->singleton(Application\SubmitPartyReview::class);
        $this->app->singleton(Application\CreateContact::class);
        $this->app->singleton(Application\CreateContactMessage::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
