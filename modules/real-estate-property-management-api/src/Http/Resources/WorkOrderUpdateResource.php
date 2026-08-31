<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class WorkOrderUpdateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'work_order_id', 'updated_by', 'update_type', 'status_change', 'description', 'progress_percentage', 'time_spent', 'materials_used', 'issues_encountered', 'next_steps', 'update_date', 'is_customer_visible', 'created_at']);
    }
}
