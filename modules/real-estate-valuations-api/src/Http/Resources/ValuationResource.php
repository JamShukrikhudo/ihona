<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ValuationsApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ValuationResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'property_id', 'party_id', 'subject', 'status', 'fee_amount', 'valued_amount', 'comparable_data', 'recommendation', 'conversion', 'scheduled_at', 'completed_at', 'follow_up_at', 'created_at', 'updated_at']);
    }
}
