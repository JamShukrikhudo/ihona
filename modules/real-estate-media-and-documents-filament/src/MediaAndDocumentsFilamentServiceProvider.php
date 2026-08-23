<?php
declare(strict_types=1);
namespace Liberu\RealEstate\MediaAndDocumentsFilament;
use Illuminate\Support\ServiceProvider;
final class MediaAndDocumentsFilamentServiceProvider extends ServiceProvider { public function register(): void { $this->app->singleton(MediaAndDocumentsFilamentPlugin::class); } }
