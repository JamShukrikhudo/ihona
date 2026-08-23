<?php
declare(strict_types=1);
namespace Liberu\RealEstate\ListingsFilament;
use Illuminate\Support\ServiceProvider;
final class ListingsFilamentServiceProvider extends ServiceProvider { public function register():void{$this->app->singleton(ListingsFilamentPlugin::class);} }
