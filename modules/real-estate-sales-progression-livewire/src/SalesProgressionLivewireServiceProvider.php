<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgressionLivewire;

use Illuminate\Support\ServiceProvider;

final class SalesProgressionLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-sales-progression-livewire');
    }
}
