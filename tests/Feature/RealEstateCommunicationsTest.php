<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Core\Application\RecordCommunication;
use Liberu\RealEstate\Core\Models\Communication;

beforeEach(function (): void {
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
});

it('records team-scoped communication history with related identity', function (): void {
    expect(Schema::hasTable('real_estate_communications'))->toBeTrue();
    $communication = app(RecordCommunication::class)->handle(10, 7, ['related_type' => 'property', 'related_id' => '12', 'channel' => 'email', 'direction' => 'outbound', 'subject' => 'Viewing confirmation', 'body' => 'Confirmed.', 'occurred_at' => '2026-09-01 10:00:00']);

    expect($communication->channel)->toBe('email')->and($communication->related_id)->toBe('12')->and(Communication::query()->forTeam(11)->count())->toBe(0);
    expect(fn () => app(RecordCommunication::class)->handle(10, 7, ['channel' => 'carrier-pigeon', 'occurred_at' => now()]))->toThrow(ValidationException::class);
});

it('exposes authenticated communication CRUD with team isolation', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $create = $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/communications', ['related_type' => 'party', 'related_id' => '4', 'channel' => 'phone', 'direction' => 'inbound', 'body' => 'Call received.', 'occurred_at' => '2026-09-01 10:00:00']);
    $create->assertCreated()->assertJsonPath('data.channel', 'phone');
    $id = $create->json('data.id');
    $this->actingAs($user, 'sanctum')->patchJson('/api/v1/real-estate/communications/'.$id, ['status' => 'recorded'])->assertOk()->assertJsonPath('data.status', 'recorded');
    $other = User::factory()->create(['current_team_id' => 11]);
    $this->actingAs($other, 'sanctum')->deleteJson('/api/v1/real-estate/communications/'.$id)->assertNotFound();
});
