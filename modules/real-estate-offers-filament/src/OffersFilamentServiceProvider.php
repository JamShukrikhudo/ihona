<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OffersFilament;

use Illuminate\Support\ServiceProvider;

final class OffersFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(OffersFilamentPlugin::class);
    }
}
