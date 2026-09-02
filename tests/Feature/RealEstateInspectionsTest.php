<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Properties\Application\CreateProperty;
use Liberu\RealEstate\PropertyManagement\Application\CreateInspection;
use Liberu\RealEstate\PropertyManagement\Application\UpdateInspection;
use Liberu\RealEstate\PropertyManagement\Domain\InspectionStatus;
use Liberu\RealEstate\PropertyManagement\Models\Inspection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

beforeEach(function (): void {
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
});

it('persists scheduled inspections with structured inspection evidence', function (): void {
    expect(Schema::hasTable('real_estate_inspections'))->toBeTrue();
    $user = User::factory()->create(['current_team_id' => 10]);
    $property = app(CreateProperty::class)->handle(10, $user->getKey(), ['address' => 'Inspection Street']);
    $inspection = app(CreateInspection::class)->handle(10, $user->getKey(), [
        'property_id' => $property->getKey(), 'type' => 'check_in', 'scheduled_at' => '2026-09-01 10:00:00',
        'areas' => [['name' => 'Kitchen', 'condition' => 'good']], 'photos' => ['kitchen.jpg'],
        'damage_reports' => [['area' => 'Wall', 'description' => 'Small mark', 'severity' => 'minor']],
    ]);

    expect($inspection->status)->toBe(InspectionStatus::Scheduled)
        ->and($inspection->areas[0]['name'])->toBe('Kitchen')
        ->and(Inspection::query()->forTeam(11)->count())->toBe(0);
});

it('enforces team isolation and prevents completed inspections reopening', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $inspection = app(CreateInspection::class)->handle(10, $user->getKey(), ['property_id' => 1, 'type' => 'routine', 'scheduled_at' => now()->addDay()]);
    $completed = app(UpdateInspection::class)->handle($inspection, 10, ['status' => 'completed', 'completed_at' => now()]);

    expect($completed->status)->toBe(InspectionStatus::Completed);
    expect(fn () => app(UpdateInspection::class)->handle($completed, 11, ['notes' => 'No access']))->toThrow(NotFoundHttpException::class);
    expect(fn () => app(UpdateInspection::class)->handle($completed, 10, ['status' => 'scheduled']))->toThrow(ValidationException::class);
});

it('exposes authenticated inspection CRUD endpoints with team isolation', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $create = $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/inspections', ['property_id' => 1, 'type' => 'routine', 'scheduled_at' => '2026-09-01 10:00:00']);
    $create->assertCreated()->assertJsonPath('data.type', 'routine');
    $id = $create->json('data.id');
    $this->actingAs($user, 'sanctum')->patchJson('/api/v1/real-estate/inspections/'.$id, ['status' => 'in_progress', 'notes' => 'Started'])->assertOk()->assertJsonPath('data.status', 'in_progress');
    $other = User::factory()->create(['current_team_id' => 11]);
    $this->actingAs($other, 'sanctum')->getJson('/api/v1/real-estate/inspections/'.$id)->assertNotFound();
});
