<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PartiesApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ContactResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return $this->resource->only([
            'id', 'team_id', 'branch_id', 'company_id', 'type', 'title', 'first_name', 'last_name',
            'emails', 'phones', 'addresses', 'tags', 'notes', 'preferred_language', 'status',
            'last_contacted_at', 'created_at', 'updated_at',
        ]);
    }
}
