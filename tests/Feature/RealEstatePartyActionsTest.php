<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Parties\Application\CreateParty;
use Liberu\RealEstate\Parties\Application\CreatePartyRelationship;
use Liberu\RealEstate\Parties\Application\DeleteParty;
use Liberu\RealEstate\Parties\Application\ManagePartyConsent;
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

it('manages consent through an explicit team-scoped action', function () {
    $party = app(CreateParty::class)->handle(10, 20, ['type' => 'applicant', 'name' => 'An Applicant']);

    app(ManagePartyConsent::class)->handle($party, 10, true);
    expect($party->refresh()->consent_at)->not->toBeNull()
        ->and($party->metadata['consent']['granted'])->toBeTrue();

    app(ManagePartyConsent::class)->handle($party, 10, false);
    expect($party->refresh()->consent_at)->toBeNull()
        ->and($party->metadata['consent']['granted'])->toBeFalse();
});

it('creates team-safe party relationships and rejects cross-team links', function (): void {
    $party = app(CreateParty::class)->handle(10, 20, ['type' => 'buyer', 'name' => 'Buyer']);
    $related = app(CreateParty::class)->handle(10, 21, ['type' => 'solicitor', 'name' => 'Solicitor']);

    $relationship = app(CreatePartyRelationship::class)->handle($party, 10, ['related_party_id' => $related->getKey(), 'relationship' => 'buyer_solicitor']);

    expect($relationship->relationship)->toBe('buyer_solicitor');
    $outside = app(CreateParty::class)->handle(11, 22, ['type' => 'vendor', 'name' => 'Other team']);
    expect(fn () => app(CreatePartyRelationship::class)->handle($party, 10, ['related_party_id' => $outside->getKey(), 'relationship' => 'untrusted']))->toThrow(ValidationException::class);
});
