<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ManagementRecordResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'property_id', 'party_id', 'subject', 'capability', 'status', 'details', 'audit', 'completed_at', 'cancelled_at', 'created_at', 'updated_at']);
    }
}
