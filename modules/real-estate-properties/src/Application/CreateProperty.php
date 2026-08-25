<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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

        return DB::transaction(function () use ($teamId, $actorId, $attributes, $address): Property {
            $property = Property::query()->create([
                'team_id' => $teamId,
                'created_by' => $actorId,
                'address' => $address,
                'title' => $attributes['title'] ?? null,
                'description' => $attributes['description'] ?? null,
                'price' => $attributes['price'] ?? null,
                'currency' => $attributes['currency'] ?? null,
                'bedrooms' => $attributes['bedrooms'] ?? null,
                'bathrooms' => $attributes['bathrooms'] ?? null,
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
                'epc' => $attributes['epc'] ?? null,
                'virtual_tour_url' => $attributes['virtual_tour_url'] ?? null,
                'virtual_tour_provider' => $attributes['virtual_tour_provider'] ?? null,
                'model_3d_url' => $attributes['model_3d_url'] ?? null,
                'floor_plan_data' => $attributes['floor_plan_data'] ?? null,
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
