<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgressionApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class SalesProgressionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'subject', 'property_id', 'party_id', 'status', 'milestones', 'chain_metadata', 'created_at', 'updated_at']);
    }
}
