<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ZooplaLivewire;

use Illuminate\Support\ServiceProvider;

final class ZooplaLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-zoopla-livewire');
    }
}
