<?php
declare(strict_types=1);
namespace Liberu\RealEstate\Instructions;
use Illuminate\Support\ServiceProvider;
final class InstructionsServiceProvider extends ServiceProvider { public function boot():void{$this->loadMigrationsFrom(__DIR__.'/../database/migrations');} }
