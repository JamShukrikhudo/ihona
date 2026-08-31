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
use Liberu\RealEstate\Properties\Application\SubmitNeighborhoodReview;
use Liberu\RealEstate\Properties\Models\Neighborhood;
use Liberu\RealEstate\Properties\Models\NeighborhoodReview;
use Liberu\RealEstate\PropertiesLivewire\Components\NeighborhoodReviewForm;
use Livewire\Livewire;

beforeEach(function (): void {
    Livewire::component('test-landlord-review-form', LandlordReviewForm::class);
    Livewire::component('test-tenant-review-form', TenantReviewForm::class);
    Livewire::component('test-neighborhood-review-form', NeighborhoodReviewForm::class);
});

it('provides team-scoped party and neighborhood review storage', function (): void {
    expect(Schema::hasTable('real_estate_party_reviews'))->toBeTrue()
        ->and(Schema::hasTable('real_estate_neighborhoods'))->toBeTrue()
        ->and(Schema::hasTable('real_estate_neighborhood_reviews'))->toBeTrue()
        ->and(Schema::hasColumn('real_estate_properties', 'neighborhood_id'))->toBeTrue();
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

it('submits one moderated neighborhood review and prevents duplicates', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $neighborhood = Neighborhood::query()->create(['team_id' => 10, 'created_by' => $user->getKey(), 'name' => 'Central']);

    Livewire::actingAs($user)->test('test-neighborhood-review-form', ['neighborhoodId' => $neighborhood->getKey()])
        ->set('rating', 4)
        ->set('title', 'Walkable and lively')
        ->set('comment', 'The area has useful amenities and good transport links.')
        ->call('submitReview')
        ->assertHasNoErrors()
        ->assertDispatched('reviewAdded', reviewId: 1);

    expect(NeighborhoodReview::query()->first())
        ->rating->toBe(4)
        ->approved->toBeFalse()
        ->moderation_status->toBe('pending');

    expect(fn () => app(SubmitNeighborhoodReview::class)->handle(10, $user->getKey(), $neighborhood->getKey(), ['rating' => 5, 'title' => 'Again', 'comment' => 'Another review is not allowed.']))->toThrow(ValidationException::class);
});

it('keeps role review forms accessible and role constrained', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $landlord = app(CreateParty::class)->handle(10, $user->getKey(), ['type' => 'landlord', 'name' => 'Landlord']);

    Livewire::actingAs($user)->test('test-landlord-review-form', ['landlordId' => $landlord->getKey()])
        ->assertSee('aria-label="1 out of 5"', escape: false)
        ->assertSee('party-review-comment', escape: false)
        ->assertSee('Submit review');
});
