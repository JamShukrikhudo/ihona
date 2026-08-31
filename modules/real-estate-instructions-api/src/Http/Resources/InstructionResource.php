<?php

declare(strict_types=1);

namespace Liberu\RealEstate\InstructionsApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class InstructionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'subject', 'property_id', 'party_id', 'status', 'ownership_check', 'terms', 'disclosures', 'approved_at', 'withdrawn_at', 'created_at', 'updated_at']);
    }
}
