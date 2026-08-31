<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class InspectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'property_id', 'party_id', 'branch_id', 'assigned_to', 'created_by', 'type', 'status', 'scheduled_at', 'started_at', 'completed_at', 'notes', 'areas', 'photos', 'damage_reports', 'signatures', 'created_at', 'updated_at']);
    }
}
