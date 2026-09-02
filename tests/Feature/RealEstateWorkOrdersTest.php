<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Liberu\RealEstate\PropertyManagement\Application\CreateWorkOrder;
use Liberu\RealEstate\PropertyManagement\Application\RecordWorkOrderUpdate;
use Liberu\RealEstate\PropertyManagement\Domain\WorkOrderStatus;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

beforeEach(function (): void {
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
});

it('persists work orders and records progress updates', function (): void {
    expect(Schema::hasTable('real_estate_work_orders'))->toBeTrue()->and(Schema::hasTable('real_estate_work_order_updates'))->toBeTrue();
    $user = User::factory()->create(['current_team_id' => 10]);
    $order = app(CreateWorkOrder::class)->handle(10, $user->getKey(), ['property_id' => 1, 'title' => 'Repair boiler', 'description' => 'Replace the failed pump.', 'work_type' => 'plumbing', 'priority' => 4]);
    $update = app(RecordWorkOrderUpdate::class)->handle($order, 10, $user->getKey(), ['update_type' => 'progress', 'description' => 'Technician attended.', 'progress_percentage' => 50, 'status_change' => 'in_progress']);

    expect($order->refresh()->status)->toBe(WorkOrderStatus::InProgress)->and($update->progress_percentage)->toBe(50)->and($order->updates()->count())->toBe(1);
});

it('enforces work-order team isolation', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $order = app(CreateWorkOrder::class)->handle(10, $user->getKey(), ['property_id' => 1, 'title' => 'Repair gate', 'description' => 'Gate is stuck.', 'work_type' => 'general']);
    expect(fn () => app(RecordWorkOrderUpdate::class)->handle($order, 11, $user->getKey(), ['update_type' => 'note', 'description' => 'No access']))->toThrow(NotFoundHttpException::class);
});

it('exposes work-order CRUD and progress update endpoints', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $create = $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/work-orders', ['property_id' => 1, 'title' => 'Repair lock', 'description' => 'Replace cylinder.', 'work_type' => 'locksmith']);
    $create->assertCreated()->assertJsonPath('data.status', 'pending');
    $id = $create->json('data.id');
    $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/work-orders/'.$id.'/updates', ['update_type' => 'progress', 'description' => 'Scheduled with vendor.', 'status_change' => 'scheduled'])->assertCreated();
    $this->actingAs($user, 'sanctum')->getJson('/api/v1/real-estate/work-orders/'.$id.'/updates')->assertOk()->assertJsonPath('data.0.status_change', 'scheduled');
});
