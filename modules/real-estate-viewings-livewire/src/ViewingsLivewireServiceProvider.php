<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ViewingsLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class ViewingsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-viewings-livewire');
        Livewire::component('module-real-estate-viewings::viewing-list', Components\ViewingList::class);
        Livewire::component('module-real-estate-viewings::viewing-booking', Components\ViewingBooking::class);
    }
}
