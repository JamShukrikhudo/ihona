<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Viewings\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Viewings\Domain\ViewingStatus;
use Liberu\RealEstate\Viewings\Models\Viewing;

final class CreateViewing
{
    public function handle(int|string $teamId, int|string $actorId, array $attributes): Viewing
    {
        $subject = trim((string) ($attributes['subject'] ?? ''));
        $starts = $attributes['starts_at'] ?? null;
        if ($subject === '') {
            throw ValidationException::withMessages(['subject' => 'A viewing subject is required.']);
        }if ($starts === null) {
            throw ValidationException::withMessages(['starts_at' => 'A viewing start time is required.']);
        }

return DB::transaction(fn (): Viewing => Viewing::query()->create(['team_id' => $teamId, 'created_by' => $actorId, 'property_id' => $attributes['property_id'] ?? null, 'party_id' => $attributes['party_id'] ?? null, 'subject' => $subject, 'status' => ViewingStatus::Requested, 'starts_at' => $starts, 'ends_at' => $attributes['ends_at'] ?? null, 'access' => $attributes['access'] ?? [], 'accompaniment' => $attributes['accompaniment'] ?? [], 'reminders' => $attributes['reminders'] ?? []]));
    }
}
