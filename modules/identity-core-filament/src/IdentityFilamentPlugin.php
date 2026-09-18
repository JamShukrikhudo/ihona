<?php

namespace Liberu\Foundation\IdentityFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\IdentityFilament\Resources\UserResource;

final class IdentityFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'liberu-identity';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([UserResource::class]);

        // UsersCluster lives in liberusoftware/filament-clusters-contracts,
        // not this package — it's shared with organizations-teams-filament's
        // TeamResource, and a Cluster has no runtime setter (unlike
        // navigationGroup()/navigationSort()), so both resources must point
        // at the exact same class without depending on each other. Both
        // packages call discoverClusters() on this same vendor directory;
        // Filament discovers by class name, so registering it twice is a
        // harmless no-op the second time.
        $panel->discoverClusters(
            in: base_path('vendor/liberusoftware/filament-clusters-contracts/src'),
            for: 'Liberu\FilamentClusters',
        );
    }

    public function boot(Panel $panel): void {}
}
