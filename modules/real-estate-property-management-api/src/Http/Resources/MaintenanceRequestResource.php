<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class MaintenanceRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'property_id', 'party_id', 'vendor_id', 'created_by', 'title', 'description', 'status', 'priority', 'requested_date', 'photos', 'quote_references', 'invoice_reference', 'payment_status', 'completed_at', 'created_at', 'updated_at']);
    }
}
