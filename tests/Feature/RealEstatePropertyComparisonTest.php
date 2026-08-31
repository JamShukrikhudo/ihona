<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Liberu\RealEstate\Properties\Application\CreateProperty;
use Liberu\RealEstate\PropertiesLivewire\Components\PropertyComparison;
use Livewire\Livewire;

it('compares only team properties in the requested order through the API', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $first = app(CreateProperty::class)->handle(10, $user->getKey(), ['address' => 'First Street', 'title' => 'First home']);
    $second = app(CreateProperty::class)->handle(10, $user->getKey(), ['address' => 'Second Street', 'title' => 'Second home']);
    $otherTeam = app(CreateProperty::class)->handle(11, $user->getKey(), ['address' => 'Private Street', 'title' => 'Private home']);

    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/real-estate/properties/compare?property_ids[]='.$second->getKey().'&property_ids[]='.$first->getKey())
        ->assertOk()
        ->assertJsonPath('data.0.id', $second->getKey())
        ->assertJsonPath('data.1.id', $first->getKey());

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/real-estate/properties/compare?property_ids[]='.$first->getKey().'&property_ids[]='.$otherTeam->getKey())
        ->assertNotFound();
});

it('searches and manages a bounded tenant-scoped Livewire comparison', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $first = app(CreateProperty::class)->handle(10, $user->getKey(), ['address' => 'First Street', 'title' => 'First home']);
    $second = app(CreateProperty::class)->handle(10, $user->getKey(), ['address' => 'Second Street', 'title' => 'Second home']);
    $third = app(CreateProperty::class)->handle(10, $user->getKey(), ['address' => 'Third Street', 'title' => 'Third home']);
    app(CreateProperty::class)->handle(11, $user->getKey(), ['address' => 'First Private Street', 'title' => 'Private home']);

    $this->actingAs($user);
    Livewire::component('test-property-comparison', PropertyComparison::class);

    Livewire::test('test-property-comparison', ['propertyIds' => [$first->getKey(), $second->getKey()]])
        ->assertSee('First home')
        ->assertSee('Second home')
        ->set('searchTerm', 'Third')
        ->assertSee('Third home')
        ->assertDontSee('Private home')
        ->call('addProperty', $third->getKey())
        ->assertCount('propertyIds', 3)
        ->call('removeProperty', $second->getKey())
        ->assertCount('propertyIds', 2);
});
