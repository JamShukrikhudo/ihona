<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CommunityEventResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'property_id', 'title', 'description', 'event_date', 'end_date', 'location', 'latitude', 'longitude', 'category', 'organizer', 'website_url', 'is_public', 'distance']);
    }
}
