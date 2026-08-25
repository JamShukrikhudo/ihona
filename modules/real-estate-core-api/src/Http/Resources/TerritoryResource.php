<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class TerritoryResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return $this->resource->only([
            'id', 'team_id', 'name', 'code', 'boundary', 'metadata', 'created_at', 'updated_at',
        ]);
    }
}
