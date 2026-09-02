<?php

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Liberu\RealEstate\Properties\Application\CreateProperty;
use Liberu\RealEstate\Properties\Application\FetchWalkabilityScores;

beforeEach(function (): void {
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
});

it('provides deterministic development scores when no Walk Score key is configured', function (): void {
    config(['services.walkscore.api_key' => null]);

    $service = app(FetchWalkabilityScores::class);
    $first = $service->handle('123 Main Street', 51.5074, -0.1278);
    $second = $service->handle('123 Main Street', 51.5074, -0.1278);

    expect($first)->toEqual($second)
        ->and($first['walkability_score'])->toBeBetween(0, 100)
        ->and($first['transit_score'])->toBeBetween(0, 100)
        ->and($first['bike_score'])->toBeBetween(0, 100)
        ->and($first['walkability_description'])->toBeString();
});

it('clamps external walkability scores and preserves descriptions', function (): void {
    config([
        'services.walkscore.api_key' => 'test-key',
        'services.walkscore.base_uri' => 'https://api.walkscore.test',
    ]);
    Http::fake([
        'api.walkscore.test/*' => Http::response([
            'walkscore' => 150,
            'description' => 'Test walkability',
            'transit' => ['score' => -10, 'description' => 'Test transit'],
            'bike' => ['score' => 75, 'description' => 'Test bike'],
        ]),
    ]);

    $scores = app(FetchWalkabilityScores::class)->handle('123 Main Street', 51.5, -0.1);

    expect($scores['walkability_score'])->toBe(100)
        ->and($scores['walkability_description'])->toBe('Test walkability')
        ->and($scores['transit_score'])->toBe(0)
        ->and($scores['bike_score'])->toBe(75);
});

it('refreshes and persists walkability scores through the team-scoped API', function (): void {
    config(['services.walkscore.api_key' => null]);
    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), [
        'address' => '123 Main Street',
        'latitude' => 51.5074,
        'longitude' => -0.1278,
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/real-estate/properties/'.$property->getKey().'/walkability')
        ->assertOk()
        ->assertJsonPath('data.walkability_score', $property->refresh()->walkability_score)
        ->assertJsonPath('data.walkability_updated_at', fn ($value): bool => filled($value));

    expect($property->fresh()->history()->where('event', 'updated')->exists())->toBeTrue();
});

it('rejects walkability refresh without coordinates', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), ['address' => 'No Coordinates']);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/real-estate/properties/'.$property->getKey().'/walkability')
        ->assertUnprocessable();
});
