<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PartiesApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PartyResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return $this->resource->only(['id', 'team_id', 'type', 'name', 'email', 'phone', 'address', 'metadata', 'created_at', 'updated_at']);
    }
}
