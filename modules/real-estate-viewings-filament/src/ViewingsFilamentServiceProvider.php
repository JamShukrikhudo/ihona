<?php
declare(strict_types=1);
namespace Liberu\RealEstate\ViewingsFilament;
use Illuminate\Support\ServiceProvider;
final class ViewingsFilamentServiceProvider extends ServiceProvider { public function register():void{$this->app->singleton(ViewingsFilamentPlugin::class);} }
