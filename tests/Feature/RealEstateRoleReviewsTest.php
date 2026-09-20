<?php

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Parties\Application\CreateParty;
use Liberu\RealEstate\Parties\Application\SubmitPartyReview;
use Liberu\RealEstate\Parties\Models\PartyReview;
use Liberu\RealEstate\PartiesLivewire\Components\LandlordReviewForm;
use Liberu\RealEstate\PartiesLivewire\Components\TenantReviewForm;
use Livewire\Livewire;

beforeEach(function (): void {
    Livewire::component('test-landlord-review-form', LandlordReviewForm::class);
    Livewire::component('test-tenant-review-form', TenantReviewForm::class);
});

it('provides team-scoped party review storage', function (): void {
    expect(Schema::hasTable('real_estate_party_reviews'))->toBeTrue();
});

it('submits landlord and tenant reviews through named modular forms', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $landlord = app(CreateParty::class)->handle(10, $user->getKey(), ['type' => 'landlord', 'name' => 'Landlord']);
    $tenant = app(CreateParty::class)->handle(10, $user->getKey(), ['type' => 'tenant', 'name' => 'Tenant']);

    Livewire::actingAs($user)->test('test-landlord-review-form', ['landlordId' => $landlord->getKey()])
        ->set('rating', 4)
        ->set('comment', 'A thoughtful and reliable landlord.')
        ->call('submitReview')
        ->assertHasNoErrors()
        ->assertDispatched('reviewAdded', reviewId: 1);

    Livewire::actingAs($user)->test('test-tenant-review-form', ['tenantId' => $tenant->getKey()])
        ->set('rating', 5)
        ->set('comment', 'A considerate and dependable tenant.')
        ->call('submitReview')
        ->assertHasNoErrors();

    expect(PartyReview::query()->forTeam(10)->count())->toBe(2)
        ->and($landlord->refresh()->approvedReviewCount())->toBe(0);
});

it('rejects an incorrect party role and cross-team party access', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $buyer = app(CreateParty::class)->handle(10, $user->getKey(), ['type' => 'buyer', 'name' => 'Buyer']);
    $landlord = app(CreateParty::class)->handle(11, $user->getKey(), ['type' => 'landlord', 'name' => 'Outside landlord']);

    expect(fn () => app(SubmitPartyReview::class)->handle(10, $user->getKey(), $buyer->getKey(), ['rating' => 5, 'comment' => 'A buyer cannot receive this role review.']))->toThrow(ValidationException::class);
    expect(fn () => app(SubmitPartyReview::class)->handle(10, $user->getKey(), $landlord->getKey(), ['rating' => 5, 'comment' => 'Should not cross a team.']))->toThrow(ModelNotFoundException::class);
});

it('keeps role review forms accessible and role constrained', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $landlord = app(CreateParty::class)->handle(10, $user->getKey(), ['type' => 'landlord', 'name' => 'Landlord']);

    Livewire::actingAs($user)->test('test-landlord-review-form', ['landlordId' => $landlord->getKey()])
        ->assertSee('aria-label="1 out of 5"', escape: false)
        ->assertSee('party-review-comment', escape: false)
        ->assertSee('Submit review');
});
