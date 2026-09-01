<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MarketingApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class NewsArticleResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'title', 'slug', 'content', 'excerpt', 'featured_image', 'is_featured', 'published_at', 'created_at', 'updated_at']);
    }
}
