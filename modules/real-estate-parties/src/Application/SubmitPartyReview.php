<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Parties\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Parties\Domain\PartyType;
use Liberu\RealEstate\Parties\Models\Party;
use Liberu\RealEstate\Parties\Models\PartyReview;

final class SubmitPartyReview
{
    /** @param array<string, mixed> $data */
    public function handle(int|string $teamId, int|string $userId, int|string $partyId, array $data): PartyReview
    {
        $party = Party::query()->forTeam($teamId)->findOrFail($partyId);

        if (! in_array($party->type, [PartyType::Landlord, PartyType::Tenant], true)) {
            throw ValidationException::withMessages(['party' => 'Only landlords and tenants can receive this review.']);
        }

        if (PartyReview::query()->forTeam($teamId)->where('party_id', $party->getKey())->where('user_id', $userId)->exists()) {
            throw ValidationException::withMessages(['review' => 'You have already reviewed this party.']);
        }

        return DB::transaction(fn (): PartyReview => PartyReview::query()->create([
            'team_id' => $teamId,
            'party_id' => $party->getKey(),
            'user_id' => $userId,
            'rating' => $data['rating'],
            'comment' => $data['comment'],
            'ip_address' => $data['ip_address'] ?? null,
        ]));
    }
}
