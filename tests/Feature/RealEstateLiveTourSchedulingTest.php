<?php

use App\Models\User;
use Liberu\RealEstate\Properties\Application\CreateProperty;
use Liberu\RealEstate\PropertiesLivewire\Components\PropertyDetail;
use Liberu\RealEstate\Viewings\Models\Viewing;
use Livewire\Livewire;

it('shows and schedules a live virtual tour through the viewing lifecycle', function (): void {
    $user = User::factory()->create(['current_team_id' => 51]);
    $property = app(CreateProperty::class)->handle(51, $user->getKey(), ['address' => '51 Virtual Way', 'live_tour_available' => true]);
    $this->actingAs($user);
    Livewire::component('test-property-live-tour', PropertyDetail::class);

    Livewire::test('test-property-live-tour', ['propertyId' => $property->getKey()])
        ->assertSee('Book a live virtual tour')
        ->call('openScheduleLiveTourModal')
        ->assertSet('showScheduleLiveTourModal', true)
        ->set('tourDate', now()->addDays(3)->toDateString())
        ->set('tourTime', '14:00')
        ->set('tourNotes', 'Please demonstrate the garden view.')
        ->call('scheduleLiveTour')
        ->assertHasNoErrors()
        ->assertSet('showScheduleLiveTourModal', false)
        ->assertDispatched('tourScheduled');

    $viewing = Viewing::query()->sole();
    expect($viewing->subject)->toBe('Live virtual tour')
        ->and($viewing->team_id)->toBe(51)
        ->and($viewing->access['mode'])->toBe('virtual')
        ->and($viewing->accompaniment['notes'])->toBe('Please demonstrate the garden view.');
});

it('validates live tour dates and hides the entry point when unavailable', function (): void {
    $user = User::factory()->create(['current_team_id' => 52]);
    $property = app(CreateProperty::class)->handle(52, $user->getKey(), ['address' => '52 Quiet Way', 'live_tour_available' => false]);
    $this->actingAs($user);
    Livewire::component('test-property-live-tour-validation', PropertyDetail::class);

    Livewire::test('test-property-live-tour-validation', ['propertyId' => $property->getKey()])
        ->assertDontSee('Book a live virtual tour')
        ->set('tourDate', now()->subDay()->toDateString())
        ->set('tourTime', '14:00')
        ->call('scheduleLiveTour')
        ->assertHasErrors(['tourDate']);

    expect(Viewing::query()->count())->toBe(0);
});
