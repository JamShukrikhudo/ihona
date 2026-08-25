<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MarketingApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class MarketingCampaignResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'name', 'channel', 'property_id', 'listing_id', 'status', 'audience', 'content', 'schedule', 'metrics', 'notes', 'created_at', 'updated_at']);
    }
}
