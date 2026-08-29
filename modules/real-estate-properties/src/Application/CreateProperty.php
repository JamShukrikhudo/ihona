<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Core\Models\Branch;
use Liberu\RealEstate\Properties\Domain\PropertyStatus;
use Liberu\RealEstate\Properties\Models\Property;

final class CreateProperty
{
    /** @param array<string, mixed> $attributes */
    public function handle(int|string $teamId, int|string $actorId, array $attributes): Property
    {
        $address = trim((string) ($attributes['address'] ?? ''));
        if ($address === '') {
            throw ValidationException::withMessages(['address' => 'An address is required.']);
        }

        $branchId = $attributes['branch_id'] ?? null;
        if ($branchId !== null && ! Branch::query()->forTeam($teamId)->whereKey($branchId)->exists()) {
            throw ValidationException::withMessages(['branch_id' => 'The branch must belong to the current team.']);
        }

        return DB::transaction(function () use ($teamId, $actorId, $attributes, $address): Property {
            $property = Property::query()->create([
                'team_id' => $teamId,
                'branch_id' => $attributes['branch_id'] ?? null,
                'created_by' => $actorId,
                'address' => $address,
                'title' => $attributes['title'] ?? null,
                'description' => $attributes['description'] ?? null,
                'price' => $attributes['price'] ?? null,
                'currency' => $attributes['currency'] ?? null,
                'bedrooms' => $attributes['bedrooms'] ?? null,
                'bathrooms' => $attributes['bathrooms'] ?? null,
                'reception_rooms' => $attributes['reception_rooms'] ?? null,
                'parking' => $attributes['parking'] ?? null,
                'gardens' => $attributes['gardens'] ?? null,
                'area_sqft' => $attributes['area_sqft'] ?? null,
                'year_built' => $attributes['year_built'] ?? null,
                'structured_address' => $attributes['structured_address'] ?? null,
                'latitude' => $attributes['latitude'] ?? null,
                'longitude' => $attributes['longitude'] ?? null,
                'postal_code' => $attributes['postal_code'] ?? null,
                'country' => $attributes['country'] ?? null,
                'tenure' => $attributes['tenure'] ?? null,
                'lease_years_remaining' => $attributes['lease_years_remaining'] ?? null,
                'service_charge' => $attributes['service_charge'] ?? null,
                'ground_rent' => $attributes['ground_rent'] ?? null,
                'energy_rating' => $attributes['energy_rating'] ?? null,
                'council_tax_band' => $attributes['council_tax_band'] ?? null,
                'energy_score' => $attributes['energy_score'] ?? null,
                'walkability_score' => $attributes['walkability_score'] ?? null,
                'walkability_description' => $attributes['walkability_description'] ?? null,
                'transit_score' => $attributes['transit_score'] ?? null,
                'transit_description' => $attributes['transit_description'] ?? null,
                'bike_score' => $attributes['bike_score'] ?? null,
                'bike_description' => $attributes['bike_description'] ?? null,
                'walkability_updated_at' => $attributes['walkability_updated_at'] ?? null,
                'epc' => $attributes['epc'] ?? null,
                'virtual_tour_url' => $attributes['virtual_tour_url'] ?? null,
                'virtual_tour_provider' => $attributes['virtual_tour_provider'] ?? null,
                'model_3d_url' => $attributes['model_3d_url'] ?? null,
                'floor_plan_data' => $attributes['floor_plan_data'] ?? null,
                'list_date' => $attributes['list_date'] ?? null,
                'sold_date' => $attributes['sold_date'] ?? null,
                'is_featured' => $attributes['is_featured'] ?? false,
                'live_tour_available' => $attributes['live_tour_available'] ?? false,
                'ar_tour_enabled' => $attributes['ar_tour_enabled'] ?? false,
                'ar_tour_settings' => $attributes['ar_tour_settings'] ?? null,
                'ar_placement_guide' => $attributes['ar_placement_guide'] ?? null,
                'ar_model_scale' => $attributes['ar_model_scale'] ?? null,
                'holographic_tour_url' => $attributes['holographic_tour_url'] ?? null,
                'holographic_provider' => $attributes['holographic_provider'] ?? null,
                'holographic_metadata' => $attributes['holographic_metadata'] ?? null,
                'holographic_enabled' => $attributes['holographic_enabled'] ?? false,
                'energy_rating_date' => $attributes['energy_rating_date'] ?? null,
                'insurance_policy_id' => $attributes['insurance_policy_id'] ?? null,
                'insurance_coverage_amount' => $attributes['insurance_coverage_amount'] ?? null,
                'insurance_premium' => $attributes['insurance_premium'] ?? null,
                'insurance_expiry_date' => $attributes['insurance_expiry_date'] ?? null,
                'jupix_id' => $attributes['jupix_id'] ?? null,
                'property_type' => $attributes['property_type'] ?? 'residential',
                'characteristics' => $attributes['characteristics'] ?? [],
                'utilities' => $attributes['utilities'] ?? [],
                'features' => $attributes['features'] ?? [],
                'status' => PropertyStatus::Draft,
            ]);

            $property->history()->create([
                'team_id' => $teamId,
                'actor_id' => $actorId,
                'event' => 'created',
                'changes' => ['status' => PropertyStatus::Draft->value],
            ]);

            return $property->fresh('history');
        });
    }
}
