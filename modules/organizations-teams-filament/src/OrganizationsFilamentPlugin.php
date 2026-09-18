<?php

namespace Liberu\Foundation\OrganizationsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\OrganizationsFilament\Resources\TeamResource;

final class OrganizationsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self;
    }

    public function getId(): string
    {
        return 'liberu-organizations';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([TeamResource::class]);

        // UsersCluster lives in liberusoftware/filament-clusters-contracts,
        // shared with identity-core-filament's UserResource — see that
        // package's own comment on why. Both packages call
        // discoverClusters() on the same vendor directory; Filament
        // discovers by class name, so this is a harmless no-op if the
        // other package already registered it first.
        $panel->discoverClusters(
            in: base_path('vendor/liberusoftware/filament-clusters-contracts/src'),
            for: 'Liberu\FilamentClusters',
        );
    }

    public function boot(Panel $panel): void {}
}
