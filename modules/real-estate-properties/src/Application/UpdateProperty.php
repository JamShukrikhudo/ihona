<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Core\Models\Branch;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\Properties\Models\PropertyCategory;
use Liberu\RealEstate\Properties\Models\PropertyTemplate;

final class UpdateProperty
{
    /** @param array<string, mixed> $attributes */
    public function handle(int|string $teamId, int|string $actorId, int|string $propertyId, array $attributes): Property
    {
        $address = array_key_exists('address', $attributes)
            ? trim((string) $attributes['address'])
            : null;

        if ($address === '') {
            throw ValidationException::withMessages(['address' => 'An address is required.']);
        }
        if (array_key_exists('status', $attributes) || array_key_exists('published_at', $attributes)) {
            throw ValidationException::withMessages(['status' => 'Property lifecycle changes must use the transition action.']);
        }

        return DB::transaction(function () use ($teamId, $actorId, $propertyId, $attributes, $address): Property {
            $property = Property::query()->forTeam($teamId)->findOrFail($propertyId);
            if (array_key_exists('branch_id', $attributes) && $attributes['branch_id'] !== null && ! Branch::query()->forTeam($teamId)->whereKey($attributes['branch_id'])->exists()) {
                throw ValidationException::withMessages(['branch_id' => 'The branch must belong to the current team.']);
            }
            if (array_key_exists('property_category_id', $attributes) && $attributes['property_category_id'] !== null && ! PropertyCategory::query()->forTeam($teamId)->whereKey($attributes['property_category_id'])->exists()) {
                throw ValidationException::withMessages(['property_category_id' => 'The category must belong to the current team.']);
            }
            if (array_key_exists('property_template_id', $attributes) && $attributes['property_template_id'] !== null && ! PropertyTemplate::query()->forTeam($teamId)->whereKey($attributes['property_template_id'])->exists()) {
                throw ValidationException::withMessages(['property_template_id' => 'The template must belong to the current team.']);
            }
            $changes = [];

            $fields = [
                'address', 'branch_id', 'title', 'description', 'price', 'currency', 'bedrooms', 'bathrooms', 'area_sqft',
                'year_built', 'reception_rooms', 'parking', 'gardens', 'structured_address', 'latitude', 'longitude', 'postal_code', 'country', 'tenure',
                'lease_years_remaining', 'service_charge', 'ground_rent', 'energy_rating', 'epc',
                'council_tax_band', 'energy_score', 'walkability_score', 'walkability_description',
                'transit_score', 'transit_description', 'bike_score', 'bike_description',
                'walkability_updated_at',
                'virtual_tour_url', 'virtual_tour_provider', 'model_3d_url', 'floor_plan_data', 'property_type', 'property_category_id', 'property_template_id',
                'characteristics', 'utilities', 'features', 'list_date', 'sold_date', 'last_synced_at', 'is_featured',
                'live_tour_available', 'ar_tour_enabled', 'ar_tour_settings', 'ar_placement_guide',
                'ar_model_scale', 'holographic_tour_url', 'holographic_provider', 'holographic_metadata',
                'holographic_enabled', 'energy_rating_date', 'insurance_policy_id',
                'insurance_coverage_amount', 'insurance_premium', 'insurance_expiry_date', 'jupix_id',
            ];

            foreach ($fields as $field) {
                if (! array_key_exists($field, $attributes)) {
                    continue;
                }

                $value = $field === 'address' ? $address : $attributes[$field];
                if ($property->getAttribute($field) !== $value) {
                    $changes[$field] = ['from' => $property->getAttribute($field), 'to' => $value];
                }
            }

            if ($changes !== []) {
                $property->fill(array_intersect_key($attributes, array_flip($fields)) + ($address === null ? [] : ['address' => $address]));
                $property->save();

                $property->history()->create([
                    'team_id' => $teamId,
                    'actor_id' => $actorId,
                    'event' => 'updated',
                    'changes' => $changes,
                ]);
            }

            return $property->fresh('history');
        });
    }
}
