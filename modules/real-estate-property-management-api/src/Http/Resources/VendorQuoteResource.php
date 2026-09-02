<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class VendorQuoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'vendor_id', 'property_id', 'maintenance_request_id', 'requested_by', 'approved_by', 'work_description', 'quote_amount', 'labor_cost', 'materials_cost', 'additional_costs', 'quote_date', 'valid_until', 'estimated_duration', 'start_date', 'completion_date', 'terms_conditions', 'status', 'notes', 'rejection_reason', 'created_at', 'updated_at']);
    }
}
