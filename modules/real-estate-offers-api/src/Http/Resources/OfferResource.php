<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OffersApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class OfferResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'property_id', 'party_id', 'amount', 'status', 'terms', 'qualification', 'negotiation', 'proof', 'decision_history', 'accepted_controls', 'offered_at', 'responded_at', 'created_at', 'updated_at']);
    }
}
