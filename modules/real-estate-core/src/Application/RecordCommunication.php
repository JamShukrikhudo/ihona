<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Core\Models\Communication;

final class RecordCommunication
{
    public function handle(int|string $teamId, int|string $actorId, array $attributes): Communication
    {
        if (! filled($attributes['channel'] ?? null) || ! in_array($attributes['channel'], ['email', 'sms', 'phone', 'note', 'letter'], true)) {
            throw ValidationException::withMessages(['channel' => 'Select a valid communication channel.']);
        }
        if (! filled($attributes['occurred_at'] ?? null)) {
            throw ValidationException::withMessages(['occurred_at' => 'An occurrence time is required.']);
        }

        return DB::transaction(fn (): Communication => Communication::query()->create([...$attributes, 'team_id' => $teamId, 'created_by' => $attributes['created_by'] ?? $actorId]));
    }
}
