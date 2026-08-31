<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\PropertyManagement\Application\CreateVendorQuote;
use Liberu\RealEstate\PropertyManagement\Application\DecideVendorQuote;
use Liberu\RealEstate\PropertyManagement\Models\VendorQuote;

beforeEach(function (): void {
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
});

it('persists valid team-scoped vendor quotes and calculates total cost', function (): void {
    expect(Schema::hasTable('real_estate_vendor_quotes'))->toBeTrue();
    $user = User::factory()->create(['current_team_id' => 10]);
    $quote = app(CreateVendorQuote::class)->handle(10, $user->getKey(), ['vendor_id' => 4, 'property_id' => 1, 'work_description' => 'Repair boiler', 'quote_amount' => 900, 'labor_cost' => 500, 'materials_cost' => 300, 'additional_costs' => 100, 'quote_date' => '2026-09-01', 'valid_until' => '2026-09-30']);

    expect($quote->status->value)->toBe('pending')->and($quote->totalCost())->toBe(900.0)->and(VendorQuote::query()->forTeam(11)->count())->toBe(0);
});

it('supports accepted and rejected quote decisions', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $quote = app(CreateVendorQuote::class)->handle(10, $user->getKey(), ['vendor_id' => 4, 'property_id' => 1, 'work_description' => 'Repair roof', 'quote_amount' => 1200, 'quote_date' => '2026-09-01', 'valid_until' => '2026-09-30']);
    $accepted = app(DecideVendorQuote::class)->handle($quote, 10, $user->getKey(), 'accepted');

    expect($accepted->status->value)->toBe('accepted')->and($accepted->approved_by)->toBe($user->getKey());
    expect(fn () => app(DecideVendorQuote::class)->handle($accepted, 10, $user->getKey(), 'rejected'))->toThrow(ValidationException::class);
});

it('exposes authenticated quote CRUD and decisions with team isolation', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $create = $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/contractor-quotes', ['vendor_id' => 4, 'property_id' => 1, 'work_description' => 'Repair door', 'quote_amount' => 300, 'quote_date' => '2026-09-01', 'valid_until' => '2026-09-30']);
    $create->assertCreated()->assertJsonPath('data.status', 'pending');
    $id = $create->json('data.id');
    $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/contractor-quotes/'.$id.'/decision', ['decision' => 'accepted'])->assertOk()->assertJsonPath('data.status', 'accepted');
    $other = User::factory()->create(['current_team_id' => 11]);
    $this->actingAs($other, 'sanctum')->getJson('/api/v1/real-estate/contractor-quotes/'.$id)->assertNotFound();
});
