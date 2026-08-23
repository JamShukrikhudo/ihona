<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ValuationsFilament;

use Illuminate\Support\ServiceProvider;

final class ValuationsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ValuationsFilamentPlugin::class);
    }
}
