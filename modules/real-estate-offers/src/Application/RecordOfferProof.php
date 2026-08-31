<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Offers\Application;

use Illuminate\Support\Facades\DB;
use Liberu\RealEstate\Offers\Models\Offer;

final class RecordOfferProof
{
    public function handle(Offer $offer, int|string $teamId, int|string $actorId, array $proof): Offer
    {
        abort_unless((string) $offer->team_id === (string) $teamId, 404);

        return DB::transaction(function () use ($offer, $teamId, $actorId, $proof): Offer {
            $offer->update(['proof' => array_merge($offer->proof ?? [], $proof)]);
            $offer->events()->create(['team_id' => $teamId, 'actor_id' => $actorId, 'event_type' => 'proof_recorded', 'amount' => $offer->amount, 'status' => $offer->status, 'changes' => ['proof' => $proof], 'occurred_at' => now()]);

            return $offer->fresh();
        });
    }
}
