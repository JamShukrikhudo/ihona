<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Application;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Core\Models\CalendarEntry;

final class CreateCalendarEntry
{
    public function handle(int|string $teamId, int|string $actorId, array $attributes): CalendarEntry
    {
        foreach (['type', 'title', 'starts_at'] as $field) {
            if (! filled($attributes[$field] ?? null)) {
                throw ValidationException::withMessages([$field => 'This field is required.']);
            }
        }
        if (! in_array($attributes['type'], ['meeting', 'reminder'], true)) {
            throw ValidationException::withMessages(['type' => 'Select a valid calendar entry type.']);
        }
        $starts = Carbon::parse($attributes['starts_at']);
        if (filled($attributes['ends_at'] ?? null) && Carbon::parse($attributes['ends_at'])->lt($starts)) {
            throw ValidationException::withMessages(['ends_at' => 'The end time must be after or equal to the start time.']);
        }
        if (filled($attributes['reminder_at'] ?? null) && Carbon::parse($attributes['reminder_at'])->gt($starts)) {
            throw ValidationException::withMessages(['reminder_at' => 'The reminder must be before or equal to the start time.']);
        }

        return DB::transaction(fn (): CalendarEntry => CalendarEntry::query()->create([...$attributes, 'team_id' => $teamId, 'created_by' => $attributes['created_by'] ?? $actorId, 'organiser_id' => $attributes['organiser_id'] ?? $actorId]));
    }
}
