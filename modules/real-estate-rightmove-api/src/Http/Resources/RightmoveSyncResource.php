<?php

declare(strict_types=1);

namespace Liberu\RealEstate\RightmoveApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class RightmoveSyncResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'property_id', 'listing_id', 'reference', 'status', 'payload', 'response', 'last_synced_at', 'created_at', 'updated_at']);
    }
}
