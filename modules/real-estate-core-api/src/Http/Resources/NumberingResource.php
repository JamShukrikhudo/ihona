<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class NumberingResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return ['key' => $this->resource['key'], 'number' => $this->resource['number']];
    }
}
