<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Liberu\RealEstate\Properties\Application\CreateProperty;
use Liberu\RealEstate\Properties\Application\DeleteProperty;
use Liberu\RealEstate\Properties\Application\RecordPropertyKey;
use Liberu\RealEstate\Properties\Application\TransitionProperty;
use Liberu\RealEstate\Properties\Application\UpdateProperty;
use Liberu\RealEstate\Properties\Application\UpsertPropertyUnit;
use Liberu\RealEstate\Properties\Domain\PropertyStatus;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\PropertiesApi\Http\Resources\PropertyKeyResource;
use Liberu\RealEstate\PropertiesApi\Http\Resources\PropertyResource;
use Liberu\RealEstate\PropertiesApi\Http\Resources\PropertyUnitResource;

final class PropertyController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        $pageSize = max(1, min($request->integer('page_size', 25), 100));
        $filters = $request->validate([
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'postal_code' => ['sometimes', 'nullable', 'string', 'max:20'],
            'needs_syncing' => ['sometimes', 'boolean'],
            'amenities' => ['sometimes', 'array', 'max:20'],
            'amenities.*' => ['string', 'max:80'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'radius' => ['sometimes', 'nullable', 'numeric', 'gt:0', 'max:500'],
            'branch_id' => ['sometimes', 'nullable', 'integer', Rule::exists('real_estate_branches', 'id')->where('team_id', $teamId)],
            'min_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'max_price' => ['sometimes', 'nullable', 'numeric', 'min:0', 'gte:min_price'],
            'min_bedrooms' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'max_bedrooms' => ['sometimes', 'nullable', 'integer', 'min:0', 'gte:min_bedrooms'],
            'min_bathrooms' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'max_bathrooms' => ['sometimes', 'nullable', 'integer', 'min:0', 'gte:min_bathrooms'],
            'min_area' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'max_area' => ['sometimes', 'nullable', 'numeric', 'min:0', 'gte:min_area'],
            'property_type' => ['sometimes', 'nullable', 'string', 'max:40'],
            'property_category_id' => ['sometimes', 'nullable', 'integer', Rule::exists('real_estate_property_categories', 'id')->where('team_id', $teamId)],
            'property_template_id' => ['sometimes', 'nullable', 'integer', Rule::exists('real_estate_property_templates', 'id')->where('team_id', $teamId)],
            'country' => ['sometimes', 'nullable', 'string', 'size:2'],
            'energy_rating' => ['sometimes', 'nullable', 'string', 'max:10'],
            'featured' => ['sometimes', 'boolean'],
            'min_energy_score' => ['sometimes', 'nullable', 'integer', 'between:0,100'],
            'min_walkability_score' => ['sometimes', 'nullable', 'integer', 'between:0,100'],
            'min_transit_score' => ['sometimes', 'nullable', 'integer', 'between:0,100'],
            'min_bike_score' => ['sometimes', 'nullable', 'integer', 'between:0,100'],
        ]);

        $query = Property::query()->forTeam($teamId)
            ->search($filters['search'] ?? null)
            ->postalCode($filters['postal_code'] ?? null)
            ->when($filters['needs_syncing'] ?? false, fn ($query) => $query->needsSyncing())
            ->hasAmenities($filters['amenities'] ?? [])
            ->when($filters['latitude'] !== null && $filters['longitude'] !== null && $filters['radius'] !== null, fn ($query) => $query->nearby($filters['latitude'], $filters['longitude'], $filters['radius']))
            ->when(array_key_exists('branch_id', $filters), fn ($query) => $query->where('branch_id', $filters['branch_id']))
            ->priceRange($filters['min_price'] ?? null, $filters['max_price'] ?? null)
            ->bedrooms($filters['min_bedrooms'] ?? null, $filters['max_bedrooms'] ?? null)
            ->bathrooms($filters['min_bathrooms'] ?? null, $filters['max_bathrooms'] ?? null)
            ->areaRange($filters['min_area'] ?? null, $filters['max_area'] ?? null)
            ->propertyType($filters['property_type'] ?? null)
            ->category($filters['property_category_id'] ?? null)
            ->country($filters['country'] ?? null)
            ->energyRating($filters['energy_rating'] ?? null)
            ->when($filters['featured'] ?? false, fn ($query) => $query->featured())
            ->minimumScore('energy_score', $filters['min_energy_score'] ?? null)
            ->minimumScore('walkability_score', $filters['min_walkability_score'] ?? null)
            ->minimumScore('transit_score', $filters['min_transit_score'] ?? null)
            ->minimumScore('bike_score', $filters['min_bike_score'] ?? null);

        return PropertyResource::collection($query->latest()->paginate($pageSize)->withQueryString())->response();
    }

    public function store(Request $request, CreateProperty $create): JsonResponse
    {
        $validated = $request->validate([
            'address' => ['required', 'string', 'max:500'],
            'branch_id' => ['sometimes', 'nullable', 'integer', Rule::exists('real_estate_branches', 'id')->where('team_id', $request->user()?->current_team_id)],
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'description_generated_at' => ['sometimes', 'nullable', 'date'],
            'internal_notes' => ['sometimes', 'nullable', 'string', 'max:10000'],
            'price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'nullable', 'string', 'size:3'],
            'bedrooms' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'bathrooms' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'reception_rooms' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'parking' => ['sometimes', 'array'],
            'gardens' => ['sometimes', 'array'],
            'area_sqft' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'year_built' => ['sometimes', 'nullable', 'integer', 'min:1066', 'max:'.((int) now()->year + 2)],
            'structured_address' => ['sometimes', 'array'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'postal_code' => ['sometimes', 'nullable', 'string', 'max:20'],
            'country' => ['sometimes', 'nullable', 'string', 'size:2'],
            'tenure' => ['sometimes', 'nullable', 'string', 'max:40'],
            'lease_years_remaining' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'service_charge' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'ground_rent' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'energy_rating' => ['sometimes', 'nullable', 'string', 'max:10'],
            'council_tax_band' => ['sometimes', 'nullable', 'string', 'max:10'],
            'energy_score' => ['sometimes', 'nullable', 'integer', 'between:0,100'],
            'walkability_score' => ['sometimes', 'nullable', 'integer', 'between:0,100'],
            'walkability_description' => ['sometimes', 'nullable', 'string'],
            'transit_score' => ['sometimes', 'nullable', 'integer', 'between:0,100'],
            'transit_description' => ['sometimes', 'nullable', 'string'],
            'bike_score' => ['sometimes', 'nullable', 'integer', 'between:0,100'],
            'bike_description' => ['sometimes', 'nullable', 'string'],
            'walkability_updated_at' => ['sometimes', 'nullable', 'date'],
            'epc' => ['sometimes', 'array'],
            'virtual_tour_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'virtual_tour_provider' => ['sometimes', 'nullable', 'string', 'max:40'],
            'model_3d_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'floor_plan_data' => ['sometimes', 'array'],
            'floor_plan_image' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'list_date' => ['sometimes', 'nullable', 'date'],
            'sold_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:list_date'],
            'last_synced_at' => ['sometimes', 'nullable', 'date'],
            'is_featured' => ['sometimes', 'boolean'],
            'live_tour_available' => ['sometimes', 'boolean'],
            'ar_tour_enabled' => ['sometimes', 'boolean'],
            'ar_tour_settings' => ['sometimes', 'array'],
            'ar_placement_guide' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'ar_model_scale' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'holographic_tour_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'holographic_provider' => ['sometimes', 'nullable', 'string', 'max:255'],
            'holographic_metadata' => ['sometimes', 'array'],
            'holographic_enabled' => ['sometimes', 'boolean'],
            'energy_rating_date' => ['sometimes', 'nullable', 'date'],
            'insurance_policy_id' => ['sometimes', 'nullable', 'integer'],
            'insurance_coverage_amount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'insurance_premium' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'insurance_expiry_date' => ['sometimes', 'nullable', 'date'],
            'jupix_id' => ['sometimes', 'nullable', 'string', 'max:255'],
            'property_type' => ['sometimes', 'string', 'max:40'],
            'property_category_id' => ['sometimes', 'nullable', 'integer', Rule::exists('real_estate_property_categories', 'id')->where('team_id', $request->user()?->current_team_id)],
            'property_template_id' => ['sometimes', 'nullable', 'integer', Rule::exists('real_estate_property_templates', 'id')->where('team_id', $request->user()?->current_team_id)],
            'characteristics' => ['sometimes', 'array'],
            'utilities' => ['sometimes', 'array'],
            'features' => ['sometimes', 'array'],
        ]);
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);

        $property = $create->handle($user->current_team_id, $user->getAuthIdentifier(), $validated);

        return (new PropertyResource($property))->response()->setStatusCode(201);
    }

    public function show(Request $request, Property $property): JsonResponse
    {
        abort_unless($request->user()?->current_team_id === $property->team_id, 404);

        return (new PropertyResource($property->load('history')))->response();
    }

    public function update(Request $request, Property $property, UpdateProperty $update): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id === $property->team_id, 404);

        $validated = $request->validate([
            'address' => ['sometimes', 'string', 'max:500'],
            'branch_id' => ['sometimes', 'nullable', 'integer', Rule::exists('real_estate_branches', 'id')->where('team_id', $request->user()?->current_team_id)],
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'description_generated_at' => ['sometimes', 'nullable', 'date'],
            'internal_notes' => ['sometimes', 'nullable', 'string', 'max:10000'],
            'price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'nullable', 'string', 'size:3'],
            'bedrooms' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'bathrooms' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'reception_rooms' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'parking' => ['sometimes', 'array'],
            'gardens' => ['sometimes', 'array'],
            'area_sqft' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'year_built' => ['sometimes', 'nullable', 'integer', 'min:1066', 'max:'.((int) now()->year + 2)],
            'structured_address' => ['sometimes', 'array'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'postal_code' => ['sometimes', 'nullable', 'string', 'max:20'],
            'country' => ['sometimes', 'nullable', 'string', 'size:2'],
            'tenure' => ['sometimes', 'nullable', 'string', 'max:40'],
            'lease_years_remaining' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'service_charge' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'ground_rent' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'energy_rating' => ['sometimes', 'nullable', 'string', 'max:10'],
            'council_tax_band' => ['sometimes', 'nullable', 'string', 'max:10'],
            'energy_score' => ['sometimes', 'nullable', 'integer', 'between:0,100'],
            'walkability_score' => ['sometimes', 'nullable', 'integer', 'between:0,100'],
            'walkability_description' => ['sometimes', 'nullable', 'string'],
            'transit_score' => ['sometimes', 'nullable', 'integer', 'between:0,100'],
            'transit_description' => ['sometimes', 'nullable', 'string'],
            'bike_score' => ['sometimes', 'nullable', 'integer', 'between:0,100'],
            'bike_description' => ['sometimes', 'nullable', 'string'],
            'walkability_updated_at' => ['sometimes', 'nullable', 'date'],
            'epc' => ['sometimes', 'array'],
            'virtual_tour_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'virtual_tour_provider' => ['sometimes', 'nullable', 'string', 'max:40'],
            'model_3d_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'floor_plan_data' => ['sometimes', 'array'],
            'floor_plan_image' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'list_date' => ['sometimes', 'nullable', 'date'],
            'sold_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:list_date'],
            'last_synced_at' => ['sometimes', 'nullable', 'date'],
            'is_featured' => ['sometimes', 'boolean'],
            'live_tour_available' => ['sometimes', 'boolean'],
            'ar_tour_enabled' => ['sometimes', 'boolean'],
            'ar_tour_settings' => ['sometimes', 'array'],
            'ar_placement_guide' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'ar_model_scale' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'holographic_tour_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'holographic_provider' => ['sometimes', 'nullable', 'string', 'max:255'],
            'holographic_metadata' => ['sometimes', 'array'],
            'holographic_enabled' => ['sometimes', 'boolean'],
            'energy_rating_date' => ['sometimes', 'nullable', 'date'],
            'insurance_policy_id' => ['sometimes', 'nullable', 'integer'],
            'insurance_coverage_amount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'insurance_premium' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'insurance_expiry_date' => ['sometimes', 'nullable', 'date'],
            'jupix_id' => ['sometimes', 'nullable', 'string', 'max:255'],
            'property_type' => ['sometimes', 'string', 'max:40'],
            'property_category_id' => ['sometimes', 'nullable', 'integer', Rule::exists('real_estate_property_categories', 'id')->where('team_id', $request->user()?->current_team_id)],
            'property_template_id' => ['sometimes', 'nullable', 'integer', Rule::exists('real_estate_property_templates', 'id')->where('team_id', $request->user()?->current_team_id)],
            'characteristics' => ['sometimes', 'array'],
            'utilities' => ['sometimes', 'array'],
            'features' => ['sometimes', 'array'],
        ]);

        return (new PropertyResource($update->handle($property->team_id, $user->getAuthIdentifier(), $property->getKey(), $validated)))->response();
    }

    public function transition(Request $request, Property $property, string $status, TransitionProperty $transition): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null && (string) $user->current_team_id === (string) $property->team_id, 404);
        $target = PropertyStatus::tryFrom($status);
        abort_unless($target !== null, 404);

        return (new PropertyResource($transition->handle($user->current_team_id, $user->getAuthIdentifier(), $property->getKey(), $target)))->response();
    }

    public function unit(Request $request, Property $property, UpsertPropertyUnit $upsert): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null && (string) $user->current_team_id === (string) $property->team_id, 404);
        $data = $request->validate(['label' => ['required', 'string', 'max:80'], 'status' => ['sometimes', 'string', 'max:40'], 'floor' => ['nullable', 'integer', 'min:0'], 'bedrooms' => ['nullable', 'integer', 'min:0'], 'bathrooms' => ['nullable', 'integer', 'min:0'], 'area_sqft' => ['nullable', 'numeric', 'min:0'], 'characteristics' => ['sometimes', 'array']]);

        return (new PropertyUnitResource($upsert->handle($property, $user->current_team_id, $data)))->response();
    }

    public function key(Request $request, Property $property, RecordPropertyKey $record): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null && (string) $user->current_team_id === (string) $property->team_id, 404);
        $data = $request->validate(['key_reference' => ['required', 'string', 'max:80'], 'quantity' => ['sometimes', 'integer', 'min:1'], 'status' => ['sometimes', 'string', 'max:40'], 'notes' => ['nullable', 'string']]);

        return (new PropertyKeyResource($record->handle($property, $user->current_team_id, $data)))->response()->setStatusCode(201);
    }

    public function destroy(Request $request, Property $property, DeleteProperty $delete): Response
    {
        $user = $request->user();
        abort_unless($user?->current_team_id === $property->team_id, 404);

        $delete->handle($property->team_id, $user->getAuthIdentifier(), $property->getKey());

        return response()->noContent();
    }
}
