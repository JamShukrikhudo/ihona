<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Liberu\RealEstate\Lettings\Application\CreateRentalCharge;
use Liberu\RealEstate\Lettings\Application\UpdateRentalCharge;
use Liberu\RealEstate\Lettings\Domain\RentalChargeStatus;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

beforeEach(function (): void {
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
});

it('persists team-scoped rental charges', function (): void {
    expect(Schema::hasTable('real_estate_rental_charges'))->toBeTrue();
    $user = User::factory()->create(['current_team_id' => 10]);
    $charge = app(CreateRentalCharge::class)->handle(10, $user->getKey(), ['property_id' => 1, 'amount' => 1500, 'charge_date' => '2026-09-01', 'description' => 'September rent', 'status' => 'pending']);

    expect($charge->status)->toBe(RentalChargeStatus::Pending);
});

it('supports payment state updates and team isolation', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $charge = app(CreateRentalCharge::class)->handle(10, $user->getKey(), ['property_id' => 1, 'amount' => 1500, 'charge_date' => '2026-09-01', 'description' => 'September rent']);
    $paid = app(UpdateRentalCharge::class)->handle($charge, 10, ['status' => 'paid']);

    expect($paid->status)->toBe(RentalChargeStatus::Paid);
    expect(fn () => app(UpdateRentalCharge::class)->handle($paid, 11, ['status' => 'overdue']))->toThrow(NotFoundHttpException::class);
});

it('exposes authenticated rental charge CRUD with team isolation', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $create = $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/rental-charges', ['property_id' => 1, 'amount' => 1500, 'charge_date' => '2026-09-01', 'description' => 'September rent']);
    $create->assertCreated()->assertJsonPath('data.status', 'pending');
    $id = $create->json('data.id');
    $this->actingAs($user, 'sanctum')->patchJson('/api/v1/real-estate/rental-charges/'.$id, ['status' => 'paid'])->assertOk()->assertJsonPath('data.status', 'paid');
    $other = User::factory()->create(['current_team_id' => 11]);
    $this->actingAs($other, 'sanctum')->deleteJson('/api/v1/real-estate/rental-charges/'.$id)->assertNotFound();
});
