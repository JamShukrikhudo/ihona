<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MatchingApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class MatchProfileResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'subject', 'party_id', 'requirements', 'affordability', 'preferences', 'alerts', 'feedback', 'exclusions', 'score', 'created_at', 'updated_at']);
    }
}
