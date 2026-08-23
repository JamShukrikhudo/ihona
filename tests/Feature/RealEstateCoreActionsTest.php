<?php

use Illuminate\Support\Facades\Schema;
use Liberu\RealEstate\Core\Application\CreateBranch;
use Liberu\RealEstate\Core\Application\DeleteBranch;
use Liberu\RealEstate\Core\Application\UpdateBranch;
use Liberu\RealEstate\Core\Models\Branch;

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
