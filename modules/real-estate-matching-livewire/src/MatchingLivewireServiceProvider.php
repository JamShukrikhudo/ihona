<?php
declare(strict_types=1);
namespace Liberu\RealEstate\MatchingLivewire;
use Illuminate\Support\ServiceProvider;
final class MatchingLivewireServiceProvider extends ServiceProvider { public function boot():void{$this->loadViewsFrom(__DIR__.'/../resources/views','real-estate-matching-livewire');} }
