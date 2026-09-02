<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Liberu\RealEstate\Properties\Application\CreateProperty;
use Liberu\RealEstate\Properties\Models\PropertyReview;
use Liberu\RealEstate\PropertiesLivewire\Components\PropertyReviewForm;
use Livewire\Livewire;

beforeEach(function (): void {
    Livewire::component('test-property-review-form', PropertyReviewForm::class);
});

it('submits a completed-viewing property review for moderation', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), [
        'address' => '1 High Street',
        'title' => 'A reviewed home',
    ]);

    DB::table('real_estate_viewings')->insert([
        'team_id' => 10,
        'created_by' => $user->getKey(),
        'property_id' => $property->getKey(),
        'subject' => 'Property viewing',
        'status' => 'completed',
        'starts_at' => now()->subHour(),
        'ends_at' => now()->subMinutes(30),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Livewire::actingAs($user)->test('test-property-review-form', ['propertyId' => $property->getKey()])
        ->set('rating', 4)
        ->set('title', 'Great home')
        ->set('comment', 'A genuinely useful viewing experience.')
        ->call('submitReview')
        ->assertHasNoErrors()
        ->assertSet('message', 'Your review was submitted for moderation.')
        ->assertDispatched('property-review-submitted', reviewId: 1);

    expect(PropertyReview::query()->first())
        ->rating->toBe(4)
        ->title->toBe('Great home')
        ->approved->toBeFalse()
        ->moderation_status->toBe('pending');
});

it('rejects a property review without a completed viewing', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), ['address' => '1 High Street']);

    Livewire::actingAs($user)->test('test-property-review-form', ['propertyId' => $property->getKey()])
        ->set('title', 'Great home')
        ->set('comment', 'A genuinely useful viewing experience.')
        ->call('submitReview')
        ->assertHasErrors('review');

    expect(PropertyReview::query()->count())->toBe(0);
});

it('renders an accessible property review form', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), [
        'address' => '1 High Street',
        'title' => 'A reviewed home',
    ]);

    Livewire::actingAs($user)->test('test-property-review-form', ['propertyId' => $property->getKey()])
        ->assertSee('Share your experience')
        ->assertSee('aria-label="1 out of 5"', escape: false)
        ->assertSee('property-review-comment', escape: false)
        ->assertSee('Submit review');
});
