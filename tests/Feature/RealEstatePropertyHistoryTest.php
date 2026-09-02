<?php

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Liberu\RealEstate\Properties\Application\CreateProperty;
use Liberu\RealEstate\Properties\Application\UpdateProperty;
use Liberu\RealEstate\Properties\Models\PropertyHistory;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
});

it('exposes team-scoped property history with normalized descriptions', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), ['address' => '1 High Street', 'price' => 250000]);
    app(UpdateProperty::class)->handle(10, $user->getKey(), $property->getKey(), ['address' => '2 High Street', 'price' => 275000]);

    $history = $property->fresh()->history()->where('event', 'updated')->latest()->firstOrFail();
    expect($history->event)->toBe('updated')
        ->and($history->getFormattedDescription())->toContain('Price changed')
        ->and($history->getPriceChangePercentage())->toBe(10.0);

    $this->actingAs($user)
        ->getJson('/api/v1/real-estate/properties/'.$property->getKey().'/history?event=updated')
        ->assertOk()
        ->assertJsonPath('data.0.event', 'updated')
        ->assertJsonPath('data.0.price_change_percentage', 10);
});

it('keeps property history queries inside the current team', function (): void {
    $owner = User::factory()->create(['current_team_id' => 10]);
    $other = User::factory()->create(['current_team_id' => 20]);
    $property = app(CreateProperty::class)->handle(10, $owner->getKey(), ['address' => '1 High Street']);
    PropertyHistory::query()->where('property_id', $property->getKey())->delete();

    $this->actingAs($other)
        ->getJson('/api/v1/real-estate/properties/'.$property->getKey().'/history')
        ->assertNotFound();
});
