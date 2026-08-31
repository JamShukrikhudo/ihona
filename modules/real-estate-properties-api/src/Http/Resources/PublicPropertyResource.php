<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Anonymous, storefront-facing property shape. Deliberately narrower than
 * the authenticated PropertyResource (no internal_notes, insurance,
 * rightmove/zoopla sync ids, ...) and deliberately does NOT expose
 * has_generator/has_wifi/mountain_view/altitude/water_source/max_guests —
 * those don't exist as columns on real_estate_properties (a known,
 * out-of-scope schema-drift bug the Filament form still renders fields
 * for — see docs/handoffs). Only real columns/relations here.
 */
final class PublicPropertyResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'address' => $this->resource->address,
            'territory_code' => $this->resource->territory?->code,
            'property_type' => $this->resource->property_type,
            'price' => $this->resource->price,
            'currency' => $this->resource->currency,
            'bedrooms' => $this->resource->bedrooms,
            'bathrooms' => $this->resource->bathrooms,
            'area_sqft' => $this->resource->area_sqft,
            'latitude' => $this->resource->latitude,
            'longitude' => $this->resource->longitude,
            'features' => $this->resource->features,
            'gallery' => array_map(static fn ($item): array => $item->toArray(), $this->resource->galleryItems()),
        ];
    }
}
