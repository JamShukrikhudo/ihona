<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\RealEstate\Properties\Models\PropertyHistory;

final class PropertyHistoryResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        /** @var PropertyHistory $history */
        $history = $this->resource;

        return [
            'id' => $history->getKey(),
            'property_id' => $history->property_id,
            'event' => $history->event,
            'changes' => $history->changes,
            'description' => $history->getFormattedDescription(),
            'price_change_percentage' => $history->getPriceChangePercentage(),
            'actor_id' => $history->actor_id,
            'created_at' => $history->created_at,
        ];
    }
}
