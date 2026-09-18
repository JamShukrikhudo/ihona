<?php

namespace Liberu\Foundation\IdentityFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\IdentityFilament\Resources\UserResource;

final class IdentityFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self;
    }

    public function getId(): string
    {
        return 'liberu-identity';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([UserResource::class]);

        // Filament has no array-based clusters() registration the way
        // resources()/pages() do — discoverClusters() (a directory scan) is
        // the only entry point. Calling it here, from the plugin, keeps the
        // host unaware of this package's internal Clusters/ directory,
        // exactly like resources() above — the alternative (the host
        // calling discoverClusters() once per package) would mean growing a
        // second, parallel per-package registry outside ModulePlugins.
        $panel->discoverClusters(in: __DIR__.'/Clusters', for: 'Liberu\Foundation\IdentityFilament\Clusters');
    }

    public function boot(Panel $panel): void {}
}
