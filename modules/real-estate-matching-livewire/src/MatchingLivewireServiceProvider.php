<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MatchingLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class MatchingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-matching-livewire');
        Livewire::addNamespace('module-real-estate-matching', classNamespace: __NAMESPACE__.'\\Components');
        Livewire::component('module-real-estate-matching::match-profile-list', Components\MatchProfileList::class);
    }
}
