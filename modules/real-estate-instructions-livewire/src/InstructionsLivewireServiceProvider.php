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
        Livewire::addNamespace('module-real-estate-instructions', classNamespace: __NAMESPACE__.'\\Components');
        Livewire::component('module-real-estate-instructions::instruction-list', Components\InstructionList::class);
    }
}
