<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Parties\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Parties\Models\Party;
use Liberu\RealEstate\Parties\Models\PartyRelationship;

final class CreatePartyRelationship
{
    /** @param array<string, mixed> $attributes */
    public function handle(Party $party, int|string $teamId, array $attributes): PartyRelationship
    {
        if ((string) $party->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['party' => 'The party does not belong to this team.']);
        }
        $relatedId = (int) ($attributes['related_party_id'] ?? 0);
        $relationship = trim((string) ($attributes['relationship'] ?? ''));
        if ($relatedId === (int) $party->getKey()) {
            throw ValidationException::withMessages(['related_party_id' => 'A party cannot relate to itself.']);
        }
        if ($relationship === '') {
            throw ValidationException::withMessages(['relationship' => 'A relationship type is required.']);
        }
        if (! Party::query()->forTeam($teamId)->whereKey($relatedId)->exists()) {
            throw ValidationException::withMessages(['related_party_id' => 'The related party does not belong to this team.']);
        }

        return PartyRelationship::query()->create(['team_id' => $teamId, 'party_id' => $party->getKey(), 'related_party_id' => $relatedId, 'relationship' => $relationship, 'metadata' => $attributes['metadata'] ?? []]);
    }
}
