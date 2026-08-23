<?php

declare(strict_types=1);

namespace Liberu\RealEstate\InstructionsFilament;

use Illuminate\Support\ServiceProvider;

final class InstructionsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(InstructionsFilamentPlugin::class);
    }
}
