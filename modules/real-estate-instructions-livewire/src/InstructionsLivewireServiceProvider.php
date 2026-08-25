<?php

declare(strict_types=1);

namespace Liberu\RealEstate\InstructionsLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class InstructionsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-instructions-livewire');
        Livewire::component('module-real-estate-instructions::instruction-list', Components\InstructionList::class);
    }
}
