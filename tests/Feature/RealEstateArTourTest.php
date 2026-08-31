<?php

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Liberu\RealEstate\Properties\Models\Property;

beforeEach(function (): void {
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
});

it('exposes public AR configuration only when enabled with a model', function (): void {
    $creator = User::factory()->create();
    $property = Property::query()->create(['team_id' => 31, 'created_by' => $creator->getKey(), 'address' => '1 Model Way', 'property_type' => 'house', 'status' => 'available', 'title' => 'Model House', 'model_3d_url' => 'https://cdn.example.test/house.glb', 'ar_tour_enabled' => true]);

    $this->getJson('/api/v1/real-estate/properties/'.$property->getKey().'/ar-tour/config')->assertOk()->assertJsonPath('config.model_url', 'https://cdn.example.test/house.glb')->assertJsonPath('property.title', 'Model House');
    $this->getJson('/api/v1/real-estate/properties/'.$property->getKey().'/ar-tour/availability')->assertOk()->assertJsonPath('stats.is_available', true);
});

it('keeps AR mutations team scoped and requires a model', function (): void {
    $user = User::factory()->create(['current_team_id' => 31]);
    $property = Property::query()->create(['team_id' => 31, 'created_by' => $user->getKey(), 'address' => '2 Model Way', 'property_type' => 'house', 'status' => 'available']);
    $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/properties/'.$property->getKey().'/ar-tour/enable')->assertUnprocessable()->assertJsonValidationErrors(['ar_tour_enabled']);

    $property->update(['model_3d_url' => 'https://cdn.example.test/house.glb']);
    $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/properties/'.$property->getKey().'/ar-tour/enable', ['ar_model_scale' => 1.5])->assertOk()->assertJsonPath('config.scale', 1.5);
    $this->actingAs($user, 'sanctum')->patchJson('/api/v1/real-estate/properties/'.$property->getKey().'/ar-tour/settings', ['auto_rotate' => false])->assertOk()->assertJsonPath('config.auto_rotate', false);
    expect($property->fresh()->ar_tour_enabled)->toBeTrue();

    $other = User::factory()->create(['current_team_id' => 32]);
    $this->actingAs($other, 'sanctum')->postJson('/api/v1/real-estate/properties/'.$property->getKey().'/ar-tour/disable')->assertNotFound();
    $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/properties/'.$property->getKey().'/ar-tour/disable')->assertOk();
    expect($property->fresh()->ar_tour_enabled)->toBeFalse();
});
