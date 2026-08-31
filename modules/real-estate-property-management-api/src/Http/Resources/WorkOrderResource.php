<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class WorkOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'property_id', 'maintenance_request_id', 'vendor_id', 'created_by', 'assigned_to', 'title', 'description', 'work_type', 'priority', 'status', 'scheduled_date', 'started_date', 'completed_date', 'estimated_cost', 'actual_cost', 'estimated_hours', 'actual_hours', 'materials_cost', 'labor_cost', 'emergency_job', 'requires_access', 'access_instructions', 'safety_requirements', 'completion_notes', 'customer_satisfaction', 'invoice_number', 'payment_status', 'created_at', 'updated_at']);
    }
}
