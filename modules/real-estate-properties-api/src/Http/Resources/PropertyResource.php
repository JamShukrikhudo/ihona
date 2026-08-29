<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\RealEstate\Properties\Models\PropertyFavorite;

final class PropertyResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $isFavorited = $user?->current_team_id !== null && PropertyFavorite::query()
            ->where('team_id', $user->current_team_id)
            ->where('user_id', $user->getAuthIdentifier())
            ->where('property_id', $this->resource->getKey())
            ->exists();

        return $this->resource->only(['id', 'team_id', 'branch_id', 'reference', 'address', 'status', 'property_type', 'property_category_id', 'property_template_id', 'bedrooms', 'bathrooms', 'price', 'area_sqft', 'service_charge', 'ground_rent', 'characteristics', 'utilities', 'features', 'structured_address', 'epc', 'floor_plan_data', 'floor_plan_image', 'latitude', 'longitude', 'last_synced_at', 'published_at', 'virtual_tour_url', 'virtual_tour_provider', 'model_3d_url', 'holographic_tour_url', 'holographic_provider', 'holographic_enabled', 'description_generated_at', 'internal_notes', 'created_at', 'updated_at']) + ['is_hmo' => $this->resource->isHmo(), 'has_active_insurance' => $this->resource->hasActiveInsurance(), 'is_favorited' => $isFavorited];
    }
}
