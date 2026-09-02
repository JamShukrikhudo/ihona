<?php

declare(strict_types=1);

namespace Liberu\RealEstate\LettingsApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class LeaseAgreementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'property_id', 'tenant_party_id', 'landlord_party_id', 'renewal_of_id', 'start_date', 'end_date', 'monthly_rent', 'security_deposit', 'status', 'payment_frequency', 'deposit_scheme', 'deposit_reference', 'terms', 'content', 'terms_and_conditions', 'is_signed', 'landlord_signed', 'tenant_signed', 'smart_contract_address', 'contract_status', 'notice_type', 'notice_served_at', 'notice_expires_at', 'ended_at', 'end_reason', 'created_at', 'updated_at']);
    }
}
