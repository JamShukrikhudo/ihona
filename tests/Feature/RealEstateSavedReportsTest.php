<?php

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Liberu\RealEstate\PortalsReporting\Models\DashboardLayout;
use Liberu\RealEstate\PortalsReporting\Models\SavedReport;

beforeEach(function (): void {
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
});

it('provides team-scoped saved reports and dashboard layouts', function (): void {
    expect(Schema::hasTable('real_estate_saved_reports'))->toBeTrue()->and(Schema::hasTable('real_estate_dashboard_layouts'))->toBeTrue();
    $user = User::factory()->create(['current_team_id' => 10]);
    $create = $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/saved-reports', ['name' => 'Pipeline', 'type' => 'pipeline', 'filters' => ['from' => '2026-01-01'], 'is_shared' => false]);
    $create->assertCreated()->assertJsonPath('data.name', 'Pipeline');
    $id = $create->json('data.id');
    $this->actingAs($user, 'sanctum')->getJson('/api/v1/real-estate/saved-reports/'.$id.'/run')->assertOk()->assertJsonPath('data.report', 'pipeline');
    $this->actingAs($user, 'sanctum')->get('/api/v1/real-estate/saved-reports/'.$id.'/export')->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
    $layout = $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/dashboard-layouts', ['name' => 'Operations', 'widgets' => [['key' => 'pipeline']]])->assertCreated()->json('data.id');
    expect(DashboardLayout::query()->find($layout)->user_id)->toBe($user->getKey());
    $other = User::factory()->create(['current_team_id' => 11]);
    $this->actingAs($other, 'sanctum')->getJson('/api/v1/real-estate/saved-reports/'.$id)->assertNotFound();
});

it('allows shared reports but protects private reports from other users', function (): void {
    $owner = User::factory()->create(['current_team_id' => 10]);
    $private = SavedReport::query()->create(['team_id' => 10, 'created_by' => $owner->getKey(), 'name' => 'Private', 'type' => 'dashboard', 'is_shared' => false]);
    $shared = SavedReport::query()->create(['team_id' => 10, 'created_by' => $owner->getKey(), 'name' => 'Shared', 'type' => 'dashboard', 'is_shared' => true]);
    $member = User::factory()->create(['current_team_id' => 10]);
    $this->actingAs($member, 'sanctum')->getJson('/api/v1/real-estate/saved-reports/'.$private->getKey())->assertNotFound();
    $this->actingAs($member, 'sanctum')->getJson('/api/v1/real-estate/saved-reports/'.$shared->getKey())->assertOk();
});
