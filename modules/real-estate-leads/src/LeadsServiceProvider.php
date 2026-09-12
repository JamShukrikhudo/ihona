<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Leads;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Liberu\RealEstate\Leads\Listeners\CreateLeadFromContactMessage;
use Liberu\RealEstate\Parties\Domain\Events\ContactMessageReceived;

final class LeadsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Application\CreateLead::class);
        $this->app->singleton(Application\UpdateLead::class);
        $this->app->singleton(Application\TransitionLead::class);
        $this->app->singleton(Application\AssignLead::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Event::listen(ContactMessageReceived::class, CreateLeadFromContactMessage::class);
    }
}
