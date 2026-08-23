<?php

use Illuminate\Support\Facades\Schema;
use Liberu\RealEstate\Parties\Application\CreateParty;
use Liberu\RealEstate\Parties\Application\DeleteParty;
use Liberu\RealEstate\Parties\Application\UpdateParty;
use Liberu\RealEstate\Parties\Models\Party;

it('creates and updates a team-scoped party', function () {
    expect(Schema::hasTable('real_estate_parties'))->toBeTrue();

    $party = app(CreateParty::class)->handle(10, 20, [
        'type' => 'buyer',
        'name' => 'A Buyer',
        'consent_at' => '2026-08-23 12:00:00',
    ]);

    $updated = app(UpdateParty::class)->handle(10, $party->getKey(), ['name' => 'A Better Buyer']);

    expect($updated->name)->toBe('A Better Buyer')
        ->and($updated->type->value)->toBe('buyer')
        ->and($updated->consent_at)->not->toBeNull();
});

it('soft deletes a party only inside its team', function () {
    $party = app(CreateParty::class)->handle(10, 20, ['type' => 'tenant', 'name' => 'A Tenant']);

    app(DeleteParty::class)->handle(10, $party->getKey());

    expect(Party::query()->find($party->getKey()))->toBeNull()
        ->and(Party::withTrashed()->find($party->getKey()))->not->toBeNull();
});
