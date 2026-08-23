<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MatchingFilament;

use Illuminate\Support\ServiceProvider;

final class MatchingFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MatchingFilamentPlugin::class);
    }
}
