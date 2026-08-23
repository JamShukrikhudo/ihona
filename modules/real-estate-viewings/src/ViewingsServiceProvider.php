<?php
declare(strict_types=1);
namespace Liberu\RealEstate\Viewings;
use Illuminate\Support\ServiceProvider;
final class ViewingsServiceProvider extends ServiceProvider { public function boot():void{$this->loadMigrationsFrom(__DIR__.'/../database/migrations');} }
