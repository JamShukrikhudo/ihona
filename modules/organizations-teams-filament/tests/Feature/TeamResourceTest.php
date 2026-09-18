<?php

use Liberu\Foundation\Organizations\Models\Team;
use Liberu\Foundation\OrganizationsFilament\Resources\TeamResource\Pages\ListTeams;
use Liberu\Foundation\OrganizationsFilament\Tests\Fixtures\OrganizationUser;
use Livewire\Livewire;

/**
 * A record is created rather than asserting `assertOk()` on an empty page: an
 * empty table renders successfully whatever is wrong with its columns, so a test
 * without rows cannot fail on the thing it is named for. `owner.name` in
 * particular only resolves if the relation and the configured user model agree.
 *
 * Skipped standalone: TeamResource now belongs to UsersCluster (shared with
 * identity-core-filament's UserResource, see liberusoftware/filament-clusters-
 * contracts) — the page's breadcrumb links to the cluster's own landing route,
 * which Filament derives from whichever cluster member it discovers first.
 * With only this package installed, that's UserResource from a sibling
 * package this testbench never boots the Filament plugin for (require-dev
 * only boots a sibling's service provider, not its panel plugin
 * registration), so the route doesn't exist yet at render time. Passes in
 * the host, where both packages are always installed together — see the
 * host's own Architecture test suite instead.
 */
it('renders the team table with its columns resolved', function () {
    // Created directly rather than through the factory: the testbench's factory
    // is bound to TestUser, so OrganizationUser::factory() still returns a
    // TestUser — and TeamPolicy type-hints the contract only this subclass has.
    $owner = OrganizationUser::forceCreate([
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.test',
        'password' => bcrypt('secret'),
    ]);

    Team::forceCreate([
        'user_id' => $owner->id,
        'name' => 'Analytical Engines',
        'personal_team' => false,
    ]);

    $this->actingAs($owner);

    Livewire::test(ListTeams::class)
        ->assertOk()
        ->assertSee('Analytical Engines')
        ->assertSee('Ada Lovelace');
})->skip('TeamResource is clustered with a sibling package\'s resource this standalone testbench never registers as a panel plugin — see the docblock above.');
