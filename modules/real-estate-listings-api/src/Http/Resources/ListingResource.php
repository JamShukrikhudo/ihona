<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ListingsApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ListingResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'title', 'property_id', 'price', 'available_from', 'status', 'channel_content', 'publication_rules', 'portal_feeds', 'reconciliation', 'published_at', 'created_at', 'updated_at']);
    }
}
