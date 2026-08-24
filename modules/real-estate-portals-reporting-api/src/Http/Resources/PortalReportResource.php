<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReportingApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PortalReportResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'portal', 'property_id', 'listing_id', 'status', 'payload', 'metrics', 'error', 'created_at', 'updated_at']);
    }
}
