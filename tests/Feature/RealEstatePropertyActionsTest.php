<?php

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Core\Application\CreateBranch;
use Liberu\RealEstate\MediaAndDocuments\Application\CreateMediaDocument;
use Liberu\RealEstate\Properties\Application\CreateProperty;
use Liberu\RealEstate\Properties\Application\DeleteProperty;
use Liberu\RealEstate\Properties\Application\DeletePropertySearch;
use Liberu\RealEstate\Properties\Application\RecordPropertyKey;
use Liberu\RealEstate\Properties\Application\SavePropertySearch;
use Liberu\RealEstate\Properties\Application\TogglePropertyFavorite;
use Liberu\RealEstate\Properties\Application\TransitionProperty;
use Liberu\RealEstate\Properties\Application\UpdateProperty;
use Liberu\RealEstate\Properties\Application\UpsertPropertyUnit;
use Liberu\RealEstate\Properties\Domain\PropertyStatus;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\Properties\Models\PropertyCategory;
use Liberu\RealEstate\Properties\Models\PropertyFavorite;
use Liberu\RealEstate\Properties\Models\PropertySavedSearch;
use Liberu\RealEstate\Properties\Models\PropertyTemplate;
use Liberu\RealEstate\PropertiesLivewire\Components\AdvancedPropertySearch;
use Liberu\RealEstate\PropertiesLivewire\Components\PropertyDetail;
use Liberu\RealEstate\PropertiesLivewire\Components\WishlistManager;
use Livewire\Livewire;

it('updates team properties and retains a change history', function () {
    expect(Schema::hasTable('real_estate_properties'))->toBeTrue();

    $property = app(CreateProperty::class)->handle(10, 20, ['address' => '1 High Street']);

    $updated = app(UpdateProperty::class)->handle(10, 20, $property->getKey(), ['address' => '2 High Street']);

    expect($updated->address)->toBe('2 High Street')
        ->and($updated->history->last()->event)->toBe('updated')
        ->and($updated->history->last()->changes['address']['to'])->toBe('2 High Street');
});

it('soft deletes team properties and retains the deletion history', function () {
    $property = app(CreateProperty::class)->handle(10, 20, ['address' => '1 High Street']);

    app(DeleteProperty::class)->handle(10, 20, $property->getKey());

    expect(Property::query()->find($property->getKey()))->toBeNull()
        ->and(Property::withTrashed()->find($property->getKey()))->not->toBeNull()
        ->and(Property::withTrashed()->find($property->getKey())->history()->where('event', 'deleted')->exists())->toBeTrue();
});

it('preserves legacy property listing attributes in the modular boundary', function () {
    $property = app(CreateProperty::class)->handle(10, 20, [
        'address' => '1 High Street',
        'title' => 'A restored legacy listing',
        'description' => 'Carried forward from the former application model.',
        'price' => 425000,
        'currency' => 'GBP',
        'bedrooms' => 3,
        'bathrooms' => 2,
        'area_sqft' => 1250,
        'year_built' => 1901,
        'postal_code' => 'SW1A 1AA',
        'virtual_tour_url' => 'https://example.test/tour',
        'model_3d_url' => 'https://example.test/model.glb',
        'is_featured' => true,
        'energy_score' => 82,
        'walkability_score' => 74,
        'reception_rooms' => 2,
    ]);

    expect($property->title)->toBe('A restored legacy listing')
        ->and($property->price)->toBe('425000.00')
        ->and($property->bedrooms)->toBe(3)
        ->and($property->virtual_tour_url)->toBe('https://example.test/tour')
        ->and($property->model3dUrl())->toBe('https://example.test/model.glb')
        ->and($property->is_featured)->toBeTrue()
        ->and($property->energy_score)->toBe(82)
        ->and($property->reception_rooms)->toBe(2);
});

it('reveals a valid 3D property model only after an explicit Livewire action', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), [
        'address' => '1 High Street',
        'model_3d_url' => 'https://example.test/model.glb',
    ]);

    $this->actingAs($user);

    Livewire::component('test-property-detail-3d', PropertyDetail::class);

    Livewire::test('test-property-detail-3d', ['propertyId' => $property->getKey()])
        ->assertSee('Show 3D model')
        ->assertDontSee('src="https://example.test/model.glb"')
        ->call('toggle3dModel')
        ->assertSet('show3dModel', true)
        ->assertSee('src="https://example.test/model.glb"', escape: false)
        ->assertSee('loading="lazy"', escape: false)
        ->assertSee('reveal="manual"', escape: false)
        ->assertDontSee('reveal="interaction"', escape: false);
});

it('renders the first team-scoped public property video without preloading it', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), ['address' => '1 High Street']);
    app(CreateMediaDocument::class)->handle(10, $user->getKey(), [
        'property_id' => $property->getKey(),
        'kind' => 'video',
        'path' => 'properties/1/tour.mp4',
        'metadata' => ['public_url' => 'https://cdn.example.test/tour.mp4'],
    ]);

    $this->actingAs($user);

    Livewire::component('test-property-detail-video', PropertyDetail::class);

    Livewire::test('test-property-detail-video', ['propertyId' => $property->getKey()])
        ->assertSee('Property video')
        ->assertSee('src="https://cdn.example.test/tour.mp4"', escape: false)
        ->assertSee('preload="none"', escape: false);
});

it('provides portable property detail disclosure facts', function (): void {
    $property = app(CreateProperty::class)->handle(10, 20, [
        'address' => '1 High Street',
        'price' => 565000,
        'area_sqft' => 1240,
        'year_built' => 1904,
        'list_date' => now()->subDays(46)->toDateString(),
        'energy_rating' => 'B',
        'energy_score' => 84,
        'epc' => ['assessment_date' => '2019-03-12'],
        'council_tax_band' => 'D',
    ]);

    expect($property->daysListed())->toBe(46)
        ->and($property->pricePerSquareFoot())->toBe(455.65)
        ->and($property->disclosureFacts()['energy']['value'])->toBe('B (84)')
        ->and($property->disclosureFacts()['energy']['source'])->toContain('2019-03-12')
        ->and($property->disclosureFacts()['council_tax_band']['value'])->toBe('D');
});

it('renders the tenant-scoped Livewire property detail surface', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), [
        'address' => '1 High Street',
        'title' => 'A disclosed property',
        'price' => 565000,
        'area_sqft' => 1240,
        'list_date' => now()->subDays(46)->toDateString(),
    ]);

    $this->actingAs($user);

    Livewire::component('test-property-detail', PropertyDetail::class);

    Livewire::test('test-property-detail', ['propertyId' => $property->getKey()])
        ->assertSee('A disclosed property')
        ->assertSee('Property facts')
        ->assertSee('46')
        ->assertSee('Book a viewing')
        ->call('requestViewing')
        ->assertDispatched('property-viewing-requested', propertyId: $property->getKey());
});

it('orders safe property gallery items and supplies a floor-plan fallback', function (): void {
    $property = app(CreateProperty::class)->handle(10, 20, [
        'address' => '1 High Street',
        'floor_plan_image' => 'https://cdn.example.test/plans/ground.png',
    ]);

    $gallery = $property->galleryItems([
        ['url' => 'https://cdn.example.test/site.png', 'kind' => 'site plan', 'caption' => 'Site plan'],
        ['url' => 'https://cdn.example.test/photo.png', 'kind' => 'photograph', 'caption' => 'Kitchen', 'staged' => true],
        ['url' => null, 'kind' => 'photograph', 'caption' => 'Private image'],
    ]);

    expect(array_map(fn ($item): string => $item->kind, $gallery))->toBe(['photograph', 'floor plan', 'site plan'])
        ->and($gallery[0]->caption)->toBe('Kitchen')
        ->and($gallery[0]->staged)->toBeTrue()
        ->and($gallery[1]->url)->toBe('https://cdn.example.test/plans/ground.png');
});

it('preserves the legacy team-scoped branch association', function () {
    $branch = app(CreateBranch::class)->handle(10, ['name' => 'Central Office', 'code' => 'CENTRAL']);
    $property = app(CreateProperty::class)->handle(10, 20, [
        'address' => '1 High Street',
        'branch_id' => $branch->getKey(),
    ]);

    expect($property->branch)->not->toBeNull()
        ->and($property->branch->getKey())->toBe($branch->getKey());

    expect(fn () => app(CreateProperty::class)->handle(11, 20, [
        'address' => '2 Low Street',
        'branch_id' => $branch->getKey(),
    ]))->toThrow(ValidationException::class);
});

it('provides reusable bounded listing filters for every presentation adapter', function () {
    app(CreateProperty::class)->handle(10, 20, [
        'address' => '1 High Street',
        'title' => 'Featured family home',
        'price' => 450000,
        'bedrooms' => 3,
        'area_sqft' => 1400,
        'property_type' => 'house',
        'country' => 'GB',
        'is_featured' => true,
        'energy_rating' => 'B',
        'energy_score' => 82,
    ]);
    app(CreateProperty::class)->handle(10, 21, [
        'address' => '2 Low Street',
        'title' => 'Small flat',
        'price' => 250000,
        'bedrooms' => 1,
        'area_sqft' => 600,
        'property_type' => 'apartment',
        'country' => 'GB',
        'energy_rating' => 'D',
        'energy_score' => 55,
    ]);

    $results = Property::query()
        ->forTeam(10)
        ->search('family')
        ->priceRange(400000, 500000)
        ->bedrooms(3, 4)
        ->areaRange(1000, 2000)
        ->propertyType('house')
        ->country('gb')
        ->energyRating('b')
        ->minimumScore('energy_score', 80)
        ->featured()
        ->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->title)->toBe('Featured family home');
});

it('centralizes property types and matches their stored casing consistently', function (): void {
    $house = app(CreateProperty::class)->handle(10, 20, [
        'address' => '1 High Street',
        'property_type' => 'House',
    ]);
    app(CreateProperty::class)->handle(10, 20, [
        'address' => '2 Low Street',
        'property_type' => 'apartment',
    ]);

    expect(Property::TYPES['hmo'])->toBe('HMO')
        ->and(Property::query()->forTeam(10)->propertyType('house')->pluck('id')->all())->toBe([$house->getKey()])
        ->and(Property::query()->forTeam(10)->propertyType('HOUSE')->pluck('id')->all())->toBe([$house->getKey()]);
});

it('preserves the legacy named property score scopes', function (): void {
    $matching = app(CreateProperty::class)->handle(10, 20, [
        'address' => '1 High Street',
        'energy_score' => 90,
        'walkability_score' => 80,
        'transit_score' => 70,
        'bike_score' => 60,
    ]);
    app(CreateProperty::class)->handle(10, 20, [
        'address' => '2 Low Street',
        'energy_score' => 50,
        'walkability_score' => 50,
        'transit_score' => 50,
        'bike_score' => 50,
    ]);

    expect(Property::query()->forTeam(10)->minEnergyScore(90)->pluck('id')->all())->toBe([$matching->getKey()])
        ->and(Property::query()->forTeam(10)->walkabilityScore(80)->pluck('id')->all())->toBe([$matching->getKey()])
        ->and(Property::query()->forTeam(10)->transitScore(70)->pluck('id')->all())->toBe([$matching->getKey()])
        ->and(Property::query()->forTeam(10)->bikeScore(60)->pluck('id')->all())->toBe([$matching->getKey()]);
});

it('supports legacy postal-prefix and stale-sync listing filters', function () {
    $stale = app(CreateProperty::class)->handle(10, 20, [
        'address' => '1 High Street',
        'postal_code' => 'SW1A 1AA',
        'last_synced_at' => now()->subDay(),
    ]);
    $current = app(CreateProperty::class)->handle(10, 20, [
        'address' => '2 High Street',
        'postal_code' => 'SW1B 2BB',
        'last_synced_at' => now()->addMinute(),
    ]);

    $results = Property::query()->forTeam(10)->postalCode('SW1A')->needsSyncing()->get();

    expect($results->pluck('id')->all())->toBe([$stale->getKey()])
        ->and(Property::query()->forTeam(10)->postalCode('SW1B')->needsSyncing()->count())->toBe(0);
});

it('supports legacy nearby-property searching in kilometres', function () {
    $near = app(CreateProperty::class)->handle(10, 20, [
        'address' => '1 High Street',
        'latitude' => 51.5074,
        'longitude' => -0.1278,
    ]);
    app(CreateProperty::class)->handle(10, 20, [
        'address' => '2 Far Street',
        'latitude' => 52.4862,
        'longitude' => -1.8904,
    ]);

    $results = Property::query()->forTeam(10)->nearby(51.5074, -0.1278, 5)->get();

    expect($results->pluck('id')->all())->toBe([$near->getKey()])
        ->and($results->first()->distance)->toBeLessThan(0.01);
});

it('supports legacy all-of amenity filtering through the modular JSON feature set', function () {
    $matching = app(CreateProperty::class)->handle(10, 20, [
        'address' => '1 High Street',
        'features' => ['garden', 'parking'],
    ]);
    app(CreateProperty::class)->handle(10, 20, [
        'address' => '2 Low Street',
        'features' => ['garden'],
    ]);

    $results = Property::query()->forTeam(10)->hasAmenities(['garden', 'parking'])->get();

    expect($results->pluck('id')->all())->toBe([$matching->getKey()]);
});

it('supports tenant-scoped property categories and category filtering', function (): void {
    $category = PropertyCategory::query()->create([
        'team_id' => 10,
        'name' => 'Investment Homes',
        'slug' => 'investment-homes',
    ]);
    $matching = app(CreateProperty::class)->handle(10, 20, [
        'address' => '1 High Street',
        'property_category_id' => $category->getKey(),
    ]);
    app(CreateProperty::class)->handle(11, 21, ['address' => '2 Low Street']);

    expect($matching->category->is($category))->toBeTrue()
        ->and(Property::query()->forTeam(10)->category($category->getKey())->pluck('id')->all())->toBe([$matching->getKey()])
        ->and(fn () => app(CreateProperty::class)->handle(11, 21, [
            'address' => '3 Other Street',
            'property_category_id' => $category->getKey(),
        ]))->toThrow(ValidationException::class);
});

it('supports tenant-scoped property templates and template filtering', function (): void {
    $template = PropertyTemplate::query()->create([
        'team_id' => 10,
        'name' => 'Investor Listing',
        'content' => '{title} — {price}',
    ]);
    $matching = app(CreateProperty::class)->handle(10, 20, [
        'address' => '1 High Street',
        'property_template_id' => $template->getKey(),
    ]);

    expect($matching->template->is($template))->toBeTrue()
        ->and(Property::query()->forTeam(10)->where('property_template_id', $template->getKey())->pluck('id')->all())->toBe([$matching->getKey()])
        ->and(fn () => app(CreateProperty::class)->handle(11, 21, [
            'address' => '2 Other Street',
            'property_template_id' => $template->getKey(),
        ]))->toThrow(ValidationException::class);
});

it('preserves legacy property metadata and year normalization', function (): void {
    $property = app(CreateProperty::class)->handle(10, 20, [
        'address' => '1 High Street',
        'year_built' => '1901-06-01',
        'description_generated_at' => now(),
        'internal_notes' => 'Review title documents.',
        'floor_plan_image' => 'https://example.test/floor-plan.png',
    ]);

    expect($property->year_built)->toBe(1901)
        ->and($property->description_generated_at)->not->toBeNull()
        ->and($property->internal_notes)->toBe('Review title documents.')
        ->and($property->floor_plan_image)->toBe('https://example.test/floor-plan.png');
});

it('centralizes legacy build-year rules and filters properties by year range', function (): void {
    $old = app(CreateProperty::class)->handle(10, 20, ['address' => '1 Old Street', 'year_built' => 1900]);
    $new = app(CreateProperty::class)->handle(10, 20, ['address' => '2 New Street', 'year_built' => 2020]);
    app(CreateProperty::class)->handle(10, 20, ['address' => '3 Other Street', 'year_built' => 2025]);

    expect(Property::EARLIEST_YEAR_BUILT)->toBe(1066)
        ->and(Property::latestYearBuilt())->toBe((int) now()->year + 2)
        ->and(Property::yearBuiltRules())->toBe(['integer', 'min:1066', 'max:'.((int) now()->year + 2)])
        ->and(Property::yearBuiltMessage())->toContain('1066')
        ->and(Property::query()->forTeam(10)->yearBuiltRange(1901, 2021)->pluck('id')->all())->toBe([$new->getKey()])
        ->and($old->year_built)->toBe(1900);
});

it('filters properties by the modular lifecycle status vocabulary', function (): void {
    $draft = app(CreateProperty::class)->handle(10, 20, ['address' => '1 Draft Street']);
    $available = app(CreateProperty::class)->handle(10, 20, ['address' => '2 Available Street']);
    app(TransitionProperty::class)->handle(10, 20, $available->getKey(), PropertyStatus::Available);

    expect(Property::query()->forTeam(10)->status(PropertyStatus::Draft)->pluck('id')->all())->toBe([$draft->getKey()])
        ->and(Property::query()->forTeam(10)->status('available')->pluck('id')->all())->toBe([$available->getKey()])
        ->and(Property::query()->forTeam(10)->status(null)->count())->toBe(2);
});

it('provides bounded property ordering for every listing surface', function (): void {
    $expensive = app(CreateProperty::class)->handle(10, 20, ['address' => 'Z Street', 'price' => 500000]);
    $affordable = app(CreateProperty::class)->handle(10, 20, ['address' => 'A Street', 'price' => 100000]);

    expect(Property::query()->forTeam(10)->sorted('price', 'asc')->pluck('id')->all())->toBe([$affordable->getKey(), $expensive->getKey()])
        ->and(Property::query()->forTeam(10)->sorted('not_allowed', 'asc')->pluck('id')->all())->toBe([$expensive->getKey(), $affordable->getKey()]);
});

it('supports tenant and user-scoped property favorites', function (): void {
    $property = app(CreateProperty::class)->handle(10, 20, ['address' => '1 High Street']);
    $otherUserProperty = app(CreateProperty::class)->handle(10, 21, ['address' => '2 High Street']);

    expect(app(TogglePropertyFavorite::class)->handle(10, 20, $property->getKey()))->toBeTrue()
        ->and(Property::query()->forTeam(10)->favoritedBy(10, 20)->pluck('id')->all())->toBe([$property->getKey()])
        ->and(PropertyFavorite::query()->where('user_id', 21)->exists())->toBeFalse()
        ->and(app(TogglePropertyFavorite::class)->handle(10, 20, $property->getKey()))->toBeFalse()
        ->and(Property::query()->forTeam(10)->favoritedBy(10, 20)->exists())->toBeFalse()
        ->and(fn () => app(TogglePropertyFavorite::class)->handle(11, 20, $otherUserProperty->getKey()))->toThrow(ModelNotFoundException::class);
});

it('serves a searchable saved-property wishlist through API and Livewire', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
    $property = app(CreateProperty::class)->handle(10, $user->getAuthIdentifier(), ['address' => '1 Saved Street', 'title' => 'Saved home', 'price' => 250000]);
    app(TogglePropertyFavorite::class)->handle(10, $user->getAuthIdentifier(), $property->getKey());

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/real-estate/properties/favorites?search=Saved')
        ->assertOk()
        ->assertJsonPath('data.0.title', 'Saved home')
        ->assertJsonPath('data.0.is_favorited', true);

    $this->deleteJson('/api/v1/real-estate/properties/favorites/'.$property->getKey())
        ->assertOk()
        ->assertJsonPath('removed', true);

    $property = app(CreateProperty::class)->handle(10, $user->getAuthIdentifier(), ['address' => '2 Shortlist Street', 'title' => 'Shortlist home']);
    app(TogglePropertyFavorite::class)->handle(10, $user->getAuthIdentifier(), $property->getKey());
    $this->actingAs($user);
    Livewire::component('test-wishlist-manager', WishlistManager::class);

    Livewire::test('test-wishlist-manager')
        ->assertSee('Shortlist home')
        ->call('removeFavorite', $property->getKey())
        ->assertSee('Removed from your shortlist.')
        ->assertSee('No saved properties yet');
});

it('persists team-scoped saved searches through core, API, and Livewire', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $otherUser = User::factory()->create(['current_team_id' => 10]);
    $saved = app(SavePropertySearch::class)->handle(10, $user->getAuthIdentifier(), 'Family homes', ['search' => 'family', 'minPrice' => 200000]);

    expect($saved)->toBeInstanceOf(PropertySavedSearch::class)
        ->and($saved->criteria)->toBe(['search' => 'family', 'minPrice' => 200000])
        ->and(PropertySavedSearch::query()->forUser(10, $otherUser->getAuthIdentifier())->count())->toBe(0)
        ->and(app(DeletePropertySearch::class)->handle(10, $otherUser->getAuthIdentifier(), $saved->getKey()))->toBeFalse();

    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/real-estate/property-saved-searches')
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Family homes');

    $created = $this->postJson('/api/v1/real-estate/property-saved-searches', ['name' => 'Apartments', 'criteria' => ['propertyType' => 'apartment']])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Apartments');

    $this->deleteJson('/api/v1/real-estate/property-saved-searches/'.$created->json('data.id'))
        ->assertOk()
        ->assertJsonPath('deleted', true);

    $this->actingAs($user);
    Livewire::component('test-advanced-property-search', AdvancedPropertySearch::class);
    Livewire::test('test-advanced-property-search', ['search' => 'family', 'savedSearchName' => 'Livewire search'])
        ->call('saveSearch')
        ->assertSet('savedSearchMessage', 'Search saved successfully.')
        ->assertSet('savedSearchName', '')
        ->call('loadSearch', PropertySavedSearch::query()->forUser(10, $user->getAuthIdentifier())->latest()->firstOrFail()->getKey())
        ->assertSet('search', 'family')
        ->call('deleteSearch', $saved->getKey())
        ->assertSet('savedSearchMessage', 'Saved search deleted.');
});

it('preserves legacy similar-property matching within the property boundary', function (): void {
    $source = app(CreateProperty::class)->handle(10, 20, [
        'address' => '1 High Street',
        'property_type' => 'house',
        'price' => 300000,
        'bedrooms' => 3,
        'bathrooms' => 2,
    ]);
    $similar = app(CreateProperty::class)->handle(10, 21, [
        'address' => '2 High Street',
        'property_type' => 'house',
        'price' => 330000,
        'bedrooms' => 4,
        'bathrooms' => 1,
    ]);
    app(CreateProperty::class)->handle(10, 22, [
        'address' => '3 High Street',
        'property_type' => 'apartment',
        'price' => 300000,
        'bedrooms' => 3,
        'bathrooms' => 2,
    ]);
    app(CreateProperty::class)->handle(11, 23, [
        'address' => '4 Other Street',
        'property_type' => 'house',
        'price' => 300000,
        'bedrooms' => 3,
        'bathrooms' => 2,
    ]);

    expect($source->similarProperties()->pluck('id')->all())->toBe([$similar->getKey()])
        ->and($source->similarProperties(0))->toHaveCount(1);
});

it('validates legacy property tour helpers and walkability freshness', function () {
    $property = app(CreateProperty::class)->handle(10, 20, [
        'address' => '1 High Street',
        'virtual_tour_url' => 'https://my.matterport.com/show/?m=abc',
        'holographic_tour_url' => 'https://example.test/holographic',
        'holographic_enabled' => true,
    ]);

    expect($property->hasVirtualTour())->toBeTrue()
        ->and($property->getVirtualTourEmbed())->toContain('https://my.matterport.com/show/?m=abc')
        ->and($property->hasHolographicTour())->toBeTrue()
        ->and($property->needsWalkabilityUpdate())->toBeTrue();

    $property->update([
        'virtual_tour_url' => 'http://my.matterport.com/show/?m=unsafe',
        'walkability_updated_at' => now(),
    ]);

    expect($property->fresh()->hasVirtualTour())->toBeFalse()
        ->and($property->fresh()->needsWalkabilityUpdate())->toBeFalse();
});

it('preserves the legacy HMO property helper across case variants', function () {
    $hmo = app(CreateProperty::class)->handle(10, 20, [
        'address' => '1 High Street',
        'property_type' => 'HMO',
    ]);
    $house = app(CreateProperty::class)->handle(10, 20, [
        'address' => '2 High Street',
        'property_type' => 'house',
    ]);

    expect($hmo->isHmo())->toBeTrue()
        ->and($house->isHmo())->toBeFalse();
});

it('preserves the legacy active insurance helper', function () {
    $property = app(CreateProperty::class)->handle(10, 20, [
        'address' => '1 High Street',
        'insurance_policy_id' => 42,
        'insurance_expiry_date' => now()->addDay(),
    ]);

    expect($property->hasActiveInsurance())->toBeTrue();

    $property->update(['insurance_expiry_date' => now()->subDay()]);

    expect($property->fresh()->hasActiveInsurance())->toBeFalse();
});

it('requires explicit property lifecycle transitions and records status history', function () {
    $property = app(CreateProperty::class)->handle(10, 20, ['address' => '1 High Street']);
    $transition = app(TransitionProperty::class);

    $property = $transition->handle(10, 20, $property->getKey(), PropertyStatus::Available);
    $property = $transition->handle(10, 20, $property->getKey(), PropertyStatus::UnderOffer);
    $property = $transition->handle(10, 20, $property->getKey(), PropertyStatus::Sold);

    expect($property->status)->toBe(PropertyStatus::Sold)
        ->and($property->published_at)->not->toBeNull()
        ->and($property->history->where('event', 'status_changed'))->toHaveCount(3)
        ->and(fn () => $transition->handle(10, 20, $property->getKey(), PropertyStatus::Available))
        ->toThrow(ValidationException::class);
});

it('owns property units and key custody records inside the property boundary', function (): void {
    $property = app(CreateProperty::class)->handle(10, 20, ['address' => '1 High Street']);

    $unit = app(UpsertPropertyUnit::class)->handle($property, 10, ['label' => 'Flat 1', 'bedrooms' => 2, 'area_sqft' => 700]);
    $key = app(RecordPropertyKey::class)->handle($property, 10, ['key_reference' => 'KEY-001', 'quantity' => 2]);

    expect($unit->label)->toBe('Flat 1')->and($unit->bedrooms)->toBe(2)
        ->and($key->key_reference)->toBe('KEY-001')->and($key->quantity)->toBe(2);
});
