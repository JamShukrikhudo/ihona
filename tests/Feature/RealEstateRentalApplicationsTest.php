<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Lettings\Application\CreateRentalApplication;
use Liberu\RealEstate\Lettings\Application\DecideRentalApplication;
use Liberu\RealEstate\Lettings\Application\UpdateRentalApplicationScreening;
use Liberu\RealEstate\Lettings\Models\RentalApplication;
use Liberu\RealEstate\Properties\Application\CreateProperty;

beforeEach(function (): void {
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
});

it('persists submitted applications and keeps them team scoped', function (): void {
    expect(Schema::hasTable('real_estate_rental_applications'))->toBeTrue();
    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), ['address' => 'Application Street', 'price' => 180000]);
    $application = app(CreateRentalApplication::class)->handle(10, $user->getKey(), ['property_id' => $property->getKey(), 'employment_status' => 'employed', 'annual_income' => 50000]);

    expect($application->status->value)->toBe('submitted')->and(RentalApplication::query()->forTeam(11)->count())->toBe(0);
    expect(fn () => app(CreateRentalApplication::class)->handle(10, $user->getKey(), ['property_id' => null]))->toThrow(ValidationException::class);
});

it('updates screening and only approves a passed application', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $application = app(CreateRentalApplication::class)->handle(10, $user->getKey(), ['property_id' => 1]);
    $screening = ['background_check_status' => 'passed', 'credit_report_status' => 'good', 'rental_history_status' => 'good', 'affordability_status' => 'passed', 'right_to_rent_status' => 'verified'];
    app(UpdateRentalApplicationScreening::class)->handle($application, 10, $screening);
    $approved = app(DecideRentalApplication::class)->handle($application->refresh(), 10, $user->getKey(), 'approved', 'All checks passed.');

    expect($approved->status->value)->toBe('approved')->and($approved->decided_by)->toBe($user->getKey());
    expect(fn () => app(DecideRentalApplication::class)->handle($approved, 10, $user->getKey(), 'rejected'))->toThrow(ValidationException::class);
});

it('exposes authenticated application CRUD, screening and decision endpoints', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $create = $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/rental-applications', ['property_id' => 1, 'employment_status' => 'student']);
    $create->assertCreated()->assertJsonPath('data.status', 'submitted');
    $id = $create->json('data.id');
    $this->actingAs($user, 'sanctum')->patchJson('/api/v1/real-estate/rental-applications/'.$id.'/screening', ['background_check_status' => 'not_required', 'credit_report_status' => 'not_required', 'rental_history_status' => 'not_available', 'affordability_status' => 'not_required', 'right_to_rent_status' => 'not_required'])->assertOk()->assertJsonPath('screening_passed', true);
    $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/rental-applications/'.$id.'/decision', ['decision' => 'approved'])->assertOk()->assertJsonPath('data.status', 'approved');
    $other = User::factory()->create(['current_team_id' => 11]);
    $this->actingAs($other, 'sanctum')->getJson('/api/v1/real-estate/rental-applications/'.$id)->assertNotFound();
});
