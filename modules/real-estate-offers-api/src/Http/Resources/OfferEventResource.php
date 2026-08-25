<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OffersApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class OfferEventResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'offer_id', 'team_id', 'status', 'payload', 'occurred_at']);
    }
}
