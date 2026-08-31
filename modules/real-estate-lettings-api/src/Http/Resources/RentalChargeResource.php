<?php

declare(strict_types=1);

namespace Liberu\RealEstate\LettingsApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class RentalChargeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'property_id', 'tenant_party_id', 'lease_agreement_id', 'amount', 'charge_date', 'description', 'status', 'created_by', 'created_at', 'updated_at']);
    }
}
