<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\PropertyManagement\Application\CreateMaintenanceRequest;
use Liberu\RealEstate\PropertyManagement\Application\UpdateMaintenanceRequest;
use Liberu\RealEstate\PropertyManagement\Domain\MaintenanceStatus;
use Liberu\RealEstate\PropertyManagement\Models\MaintenanceRequest;

beforeEach(function (): void {
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
});

it('persists team-scoped maintenance requests with vendor workflow fields', function (): void {
    expect(Schema::hasTable('real_estate_maintenance_requests'))->toBeTrue();
    $user = User::factory()->create(['current_team_id' => 10]);
    $request = app(CreateMaintenanceRequest::class)->handle(10, $user->getKey(), ['property_id' => 1, 'title' => 'Leaking tap', 'description' => 'Kitchen tap is leaking.', 'requested_date' => '2026-09-01', 'priority' => 'urgent', 'photos' => ['tap.jpg'], 'quote_references' => ['Q-1']]);

    expect($request->priority->value)->toBe('urgent')->and($request->photos)->toBe(['tap.jpg'])->and(MaintenanceRequest::query()->forTeam(11)->count())->toBe(0);
});

it('supports lifecycle updates while protecting completed work', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $request = app(CreateMaintenanceRequest::class)->handle(10, $user->getKey(), ['property_id' => 1, 'title' => 'Broken lock', 'description' => 'Front door lock is broken.', 'requested_date' => now()->toDateString()]);
    $completed = app(UpdateMaintenanceRequest::class)->handle($request, 10, ['status' => 'completed', 'completed_at' => now(), 'payment_status' => 'paid']);

    expect($completed->status)->toBe(MaintenanceStatus::Completed);
    expect(fn () => app(UpdateMaintenanceRequest::class)->handle($completed, 10, ['status' => 'pending']))->toThrow(ValidationException::class);
});

it('exposes authenticated maintenance CRUD with team isolation', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $create = $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/maintenance', ['property_id' => 1, 'title' => 'No heating', 'description' => 'Heating is unavailable.', 'requested_date' => '2026-09-01', 'priority' => 'high']);
    $create->assertCreated()->assertJsonPath('data.priority', 'high');
    $id = $create->json('data.id');
    $this->actingAs($user, 'sanctum')->patchJson('/api/v1/real-estate/maintenance/'.$id, ['status' => 'in_progress'])->assertOk()->assertJsonPath('data.status', 'in_progress');
    $other = User::factory()->create(['current_team_id' => 11]);
    $this->actingAs($other, 'sanctum')->getJson('/api/v1/real-estate/maintenance/'.$id)->assertNotFound();
});
