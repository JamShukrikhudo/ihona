<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Core\Application\CreateCalendarEntry;
use Liberu\RealEstate\Core\Models\CalendarEntry;

beforeEach(function (): void {
    RateLimiter::for('api', static fn (): Limit => Limit::perMinute(1000));
});

it('records calendar entries with attendees and recurrence', function (): void {
    expect(Schema::hasTable('real_estate_calendar_entries'))->toBeTrue();
    $entry = app(CreateCalendarEntry::class)->handle(10, 7, ['type' => 'meeting', 'title' => 'Viewing preparation', 'starts_at' => '2026-09-01 10:00:00', 'ends_at' => '2026-09-01 11:00:00', 'reminder_at' => '2026-09-01 09:30:00', 'attendee_user_ids' => [8], 'recurrence' => ['frequency' => 'weekly']]);

    expect($entry->title)->toBe('Viewing preparation')->and($entry->attendee_user_ids)->toBe([8])->and(CalendarEntry::query()->forTeam(11)->count())->toBe(0);
    expect(fn () => app(CreateCalendarEntry::class)->handle(10, 7, ['type' => 'meeting', 'title' => 'Invalid', 'starts_at' => '2026-09-01 10:00:00', 'ends_at' => '2026-09-01 09:00:00']))->toThrow(ValidationException::class);
});

it('exposes authenticated calendar CRUD with team isolation', function (): void {
    $user = User::factory()->create(['current_team_id' => 10]);
    $create = $this->actingAs($user, 'sanctum')->postJson('/api/v1/real-estate/calendar-entries', ['type' => 'reminder', 'title' => 'Renewal reminder', 'starts_at' => '2026-09-01 10:00:00']);
    $create->assertCreated()->assertJsonPath('data.type', 'reminder');
    $id = $create->json('data.id');
    $this->actingAs($user, 'sanctum')->patchJson('/api/v1/real-estate/calendar-entries/'.$id, ['status' => 'completed'])->assertOk()->assertJsonPath('data.status', 'completed');
    $other = User::factory()->create(['current_team_id' => 11]);
    $this->actingAs($other, 'sanctum')->deleteJson('/api/v1/real-estate/calendar-entries/'.$id)->assertNotFound();
});
