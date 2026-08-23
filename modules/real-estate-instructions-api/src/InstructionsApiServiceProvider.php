<?php
declare(strict_types=1);
namespace Liberu\RealEstate\InstructionsApi;
use Illuminate\Support\ServiceProvider;
final class InstructionsApiServiceProvider extends ServiceProvider { public function boot():void{$this->loadRoutesFrom(__DIR__.'/../routes/api.php');} }
