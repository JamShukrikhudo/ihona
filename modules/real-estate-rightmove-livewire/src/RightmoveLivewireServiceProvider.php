<?php

declare(strict_types=1);

namespace Liberu\RealEstate\RightmoveLivewire;

use Illuminate\Support\ServiceProvider;

final class RightmoveLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-rightmove-livewire');
    }
}
