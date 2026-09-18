<?php

namespace Liberu\Foundation\IdentityFilament\Clusters;

use Filament\Clusters\Cluster;

/**
 * Groups everything about a user account under one sidebar entry instead of
 * each resource sitting as its own top-level item. Lives in this package
 * (not the host's App\Filament\Clusters) so identity-core-filament's own
 * UserResource can reference it without creating a reverse dependency on
 * the host — the architecture rule this fleet enforces is "no App\
 * dependency", and a Cluster is assigned via a static property read at
 * class-definition time, not something the host can inject at runtime the
 * way NavigationGroups::configure() does for navigationGroup()/
 * navigationSort() (Filament's BelongsToCluster trait only exposes
 * getCluster(), no setter).
 */
class UsersCluster extends Cluster
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Users';
}
