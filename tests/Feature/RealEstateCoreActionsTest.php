<?php

use Illuminate\Support\Facades\Schema;
use Liberu\Foundation\Organizations\Models\Team;
use Liberu\RealEstate\Core\Application\CreateAgency;
use Liberu\RealEstate\Core\Application\CreateBranch;
use Liberu\RealEstate\Core\Application\CreateTerritory;
use Liberu\RealEstate\Core\Application\DeleteAgency;
use Liberu\RealEstate\Core\Application\DeleteBranch;
use Liberu\RealEstate\Core\Application\DeleteTerritory;
use Liberu\RealEstate\Core\Application\UpdateAgency;
use Liberu\RealEstate\Core\Application\UpdateBranch;
use Liberu\RealEstate\Core\Application\UpdateTerritory;
use Liberu\RealEstate\Core\Models\Agency;
use Liberu\RealEstate\Core\Models\Branch;
use Liberu\RealEstate\Core\Models\Territory;

it('creates and updates a team branch with a normalized code', function () {
    expect(Schema::hasTable('real_estate_branches'))->toBeTrue();

    $branch = app(CreateBranch::class)->handle(10, ['name' => 'Central Office', 'code' => 'central-1']);
    $updated = app(UpdateBranch::class)->handle(10, $branch->getKey(), ['name' => 'Central Branch', 'code' => 'central-2']);

    expect($updated->name)->toBe('Central Branch')
        ->and($updated->code)->toBe('CENTRAL-2');
});

it('archives only the branch inside the current team', function () {
    $branch = app(CreateBranch::class)->handle(10, ['name' => 'North Office', 'code' => 'NORTH']);

    app(DeleteBranch::class)->handle(10, $branch->getKey());

    expect(Branch::query()->find($branch->getKey()))->toBeNull()
        ->and(Branch::withTrashed()->find($branch->getKey()))->not->toBeNull();
});

it('exposes the owning team relationship required by the tenant resource', function () {
    $team = Team::factory()->create();
    $branch = app(CreateBranch::class)->handle($team->getKey(), [
        'name' => 'Tenant Office',
        'code' => 'TENANT',
    ]);

    expect($branch->team)->toBeInstanceOf(Team::class)
        ->and($branch->team->getKey())->toBe($team->getKey())
        ->and(Branch::query()->forTeam($team->getKey())->find($branch->getKey()))->not->toBeNull()
        ->and(Branch::query()->forTeam($team->getKey() + 1)->find($branch->getKey()))->toBeNull();
});

it('supports team-scoped agency and territory lifecycles', function () {
    $agency = app(CreateAgency::class)->handle(10, ['name' => 'Central Agency', 'code' => 'CENTRAL']);
    $territory = app(CreateTerritory::class)->handle(10, ['name' => 'Central Territory', 'code' => 'CENTRAL']);

    $agency = app(UpdateAgency::class)->handle(10, $agency->getKey(), ['name' => 'Central Homes']);
    $territory = app(UpdateTerritory::class)->handle(10, $territory->getKey(), ['boundary' => ['postcode_prefixes' => ['SW1']]]);

    expect($agency->name)->toBe('Central Homes')
        ->and($territory->boundary)->toBe(['postcode_prefixes' => ['SW1']])
        ->and(Agency::query()->forTeam(11)->find($agency->getKey()))->toBeNull()
        ->and(Territory::query()->forTeam(11)->find($territory->getKey()))->toBeNull();

    app(DeleteAgency::class)->handle(10, $agency->getKey());
    app(DeleteTerritory::class)->handle(10, $territory->getKey());

    expect(Agency::withTrashed()->find($agency->getKey()))->not->toBeNull()
        ->and(Territory::withTrashed()->find($territory->getKey()))->not->toBeNull();
});
