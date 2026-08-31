<?php

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Properties\Application\CheckPriceAlerts;
use Liberu\RealEstate\Properties\Application\CreatePriceAlert;
use Liberu\RealEstate\Properties\Application\CreateProperty;
use Liberu\RealEstate\Properties\Domain\Events\PriceAlertTriggered;
use Liberu\RealEstate\Properties\Models\PropertyPriceAlert;
use Liberu\RealEstate\PropertiesLivewire\Components\PriceAlertManager;
use Livewire\Livewire;

beforeEach(function (): void {
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
    Livewire::component('test-price-alert-manager', PriceAlertManager::class);
});

it('provides team-scoped price alert storage', function (): void {
    expect(Schema::hasTable('real_estate_property_price_alerts'))->toBeTrue();
});

it('creates and manages price alerts through the Livewire surface', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), ['address' => '1 Alert Street', 'price' => 250000]);

    Livewire::actingAs($user)->test('test-price-alert-manager', ['propertyId' => $property->getKey()])
        ->set('alertPercentage', 5)
        ->set('alertFrequency', 'weekly')
        ->call('createAlert')
        ->assertHasNoErrors()
        ->assertSee('Price alert created successfully.');

    $alert = PropertyPriceAlert::query()->first();
    expect($alert)->not->toBeNull()
        ->and($alert->initial_price)->toBe(250000.0)
        ->and($alert->alert_frequency)->toBe('weekly')
        ->and($alert->is_active)->toBeTrue();

    Livewire::actingAs($user)->test('test-price-alert-manager', ['propertyId' => $property->getKey()])
        ->call('toggleAlert', $alert->getKey())
        ->assertSee('Resume');

    expect($alert->refresh()->is_active)->toBeFalse();

    Livewire::actingAs($user)->test('test-price-alert-manager', ['propertyId' => $property->getKey()])
        ->call('deleteAlert', $alert->getKey());

    expect(PropertyPriceAlert::query()->count())->toBe(0);
});

it('enforces alert validation and team isolation', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(11, $user->getKey(), ['address' => 'Outside Street', 'price' => 100000]);

    expect(fn () => app(CreatePriceAlert::class)->handle(10, $user->getKey(), $property->getKey(), ['alert_percentage' => 5, 'alert_frequency' => 'daily']))->toThrow(ModelNotFoundException::class);
    expect(fn () => app(CreatePriceAlert::class)->handle(11, $user->getKey(), $property->getKey(), ['alert_percentage' => 0, 'alert_frequency' => 'daily']))->toThrow(ValidationException::class);
});

it('emits a threshold event and resets the comparison price', function (): void {
    Event::fake([PriceAlertTriggered::class]);
    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), ['address' => 'Moving Street', 'price' => 200000]);
    $alert = app(CreatePriceAlert::class)->handle(10, $user->getKey(), $property->getKey(), ['alert_percentage' => 5, 'alert_frequency' => 'daily']);
    $property->update(['price' => 190000]);

    expect(app(CheckPriceAlerts::class)->handle(10))->toBe(1);
    Event::assertDispatched(PriceAlertTriggered::class, fn (PriceAlertTriggered $event): bool => $event->alert->is($alert) && $event->percentageChange === -5.0);
    expect($alert->refresh()->initial_price)->toBe(190000.0)
        ->and(app(CheckPriceAlerts::class)->handle(10))->toBe(0);
});

it('exposes authenticated CRUD endpoints with team isolation', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), ['address' => 'API Alert Street', 'price' => 300000]);

    $create = $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/properties/'.$property->getKey().'/price-alerts', [
        'alert_percentage' => 7.5,
        'alert_frequency' => 'monthly',
    ]);

    $create->assertCreated()->assertJsonPath('data.property_id', $property->getKey());
    $alertId = $create->json('data.id');

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/real-estate/properties/'.$property->getKey().'/price-alerts')
        ->assertOk()
        ->assertJsonPath('data.0.alert_frequency', 'monthly');

    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/v1/real-estate/price-alerts/'.$alertId, ['is_active' => false])
        ->assertOk()
        ->assertJsonPath('data.is_active', false);

    $otherUser = User::factory()->create(['current_team_id' => 11]);
    $this->actingAs($otherUser, 'sanctum')
        ->deleteJson('/api/v1/real-estate/price-alerts/'.$alertId)
        ->assertNotFound();

    $this->actingAs($user, 'sanctum')
        ->deleteJson('/api/v1/real-estate/price-alerts/'.$alertId)
        ->assertOk()
        ->assertJsonPath('deleted', true);
});
