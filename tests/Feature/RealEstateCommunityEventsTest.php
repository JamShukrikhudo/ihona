<?php

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Liberu\RealEstate\Properties\Application\CreateProperty;
use Liberu\RealEstate\Properties\Models\CommunityEvent;

it('exposes upcoming public community events with category and property proximity filters', function (): void {
    expect(Schema::hasTable('real_estate_property_community_events'))->toBeTrue();

    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), ['address' => '1 High Street', 'latitude' => 51.5074, 'longitude' => -0.1278]);
    CommunityEvent::query()->create(['team_id' => null, 'title' => 'Local festival', 'event_date' => now()->addDays(5), 'category' => 'festival', 'latitude' => 51.5100, 'longitude' => -0.1300, 'is_public' => true]);
    CommunityEvent::query()->create(['team_id' => null, 'title' => 'Distant festival', 'event_date' => now()->addDays(5), 'category' => 'festival', 'latitude' => 52.4862, 'longitude' => -1.8904, 'is_public' => true]);
    CommunityEvent::query()->create(['team_id' => null, 'title' => 'Past market', 'event_date' => now()->subDay(), 'category' => 'market', 'is_public' => true]);

    expect($property->getNearbyCommunityEvents(10))->toHaveCount(1);

    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/real-estate/community-events?category=festival&property_id='.$property->getKey())
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Local festival');
});

it('does not expose another team private event', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $event = CommunityEvent::query()->create(['team_id' => 20, 'title' => 'Private meeting', 'event_date' => now()->addDay(), 'is_public' => false]);

    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
    $this->actingAs($user, 'sanctum')->getJson('/api/v1/real-estate/community-events/'.$event->getKey())->assertForbidden();
});
