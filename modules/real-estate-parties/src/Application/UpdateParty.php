<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Parties\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Parties\Models\Party;

final class UpdateParty
{
    /** @param array<string, mixed> $attributes */
    public function handle(int|string $teamId, int|string $partyId, array $attributes): Party
    {
        if (array_key_exists('name', $attributes) && trim((string) $attributes['name']) === '') {
            throw ValidationException::withMessages(['name' => 'A party name is required.']);
        }

        return DB::transaction(function () use ($teamId, $partyId, $attributes): Party {
            $party = Party::query()->forTeam($teamId)->findOrFail($partyId);
            $party->fill($attributes);
            $party->save();

            return $party->refresh();
        });
    }
}
