<?php
declare(strict_types=1);
namespace Liberu\RealEstate\InstructionsLivewire;
use Illuminate\Support\ServiceProvider;
final class InstructionsLivewireServiceProvider extends ServiceProvider { public function boot():void{$this->loadViewsFrom(__DIR__.'/../resources/views','real-estate-instructions-livewire');} }
