<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Parties\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Parties\Domain\PartyType;
use Liberu\RealEstate\Parties\Models\Party;

final class CreateParty
{
    /** @param array<string, mixed> $attributes */
    public function handle(int|string $teamId, int|string $actorId, array $attributes): Party
    {
        $name = trim((string) ($attributes['name'] ?? ''));
        if ($name === '') {
            throw ValidationException::withMessages(['name' => 'A party name is required.']);
        }

        $type = PartyType::tryFrom((string) ($attributes['type'] ?? ''));
        if ($type === null) {
            throw ValidationException::withMessages(['type' => 'A valid party type is required.']);
        }

        return DB::transaction(fn (): Party => Party::query()->create([
            'team_id' => $teamId,
            'created_by' => $actorId,
            'type' => $type,
            'name' => $name,
            'email' => $attributes['email'] ?? null,
            'phone' => $attributes['phone'] ?? null,
            'metadata' => $attributes['metadata'] ?? [],
            'consent_at' => $attributes['consent_at'] ?? null,
        ]));
    }
}
