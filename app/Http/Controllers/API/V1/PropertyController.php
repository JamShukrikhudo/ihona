<?php

namespace App\Http\Controllers\API\V1;

use App\Models\OrganisationProfile;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PropertyController
{
    public function index(Request $request): JsonResponse
    {
        $query = Property::query()
            ->where('team_id', $this->teamId($request))
            ->with(['images', 'features']);

        foreach (['status', 'property_type', 'branch_id', 'bedrooms', 'country', 'is_featured'] as $field) {
            if ($request->filled("filter.$field")) {
                $query->where($field, $request->input("filter.$field"));
            }
        }

        if ($request->filled('filter.min_price')) {
            $query->where('price', '>=', $request->input('filter.min_price'));
        }
        if ($request->filled('filter.max_price')) {
            $query->where('price', '<=', $request->input('filter.max_price'));
        }
        if ($request->filled('search')) {
            $term = '%'.$request->string('search')->trim().'%';
            $query->where(fn ($query) => $query
                ->where('title', 'like', $term)
                ->orWhere('location', 'like', $term)
                ->orWhere('postal_code', 'like', $term));
        }

        $paginator = $query->latest('id')->paginate(min(max($request->integer('per_page', 20), 1), 100));
        $paginator->getCollection()->each(fn (Property $property) => $this->includeFeatureNames($property));

        return response()->json($paginator);
    }

    public function show(Request $request, int $property): JsonResponse
    {
        $record = Property::query()
            ->where('team_id', $this->teamId($request))
            ->with(['images', 'features', 'rooms', 'bookings'])
            ->findOrFail($property);

        return response()->json(['data' => $this->includeFeatureNames($record)]);
    }

    public function store(Request $request): JsonResponse
    {
        $attributes = $request->validate($this->rules($request));
        $featureNames = $attributes['feature_names'] ?? [];
        unset($attributes['feature_names']);
        $attributes['team_id'] = $this->teamId($request);
        $attributes['user_id'] = $request->user()->id;
        $attributes['agent_id'] ??= $request->user()->id;
        $attributes['status'] ??= 'draft';
        $attributes['list_date'] ??= now()->toDateString();
        $profile = OrganisationProfile::where('team_id', $attributes['team_id'])->first();
        $attributes['country'] = strtoupper($attributes['country'] ?? $profile?->primary_country ?? 'GB');
        $attributes['currency'] = strtoupper(
            $attributes['currency']
                ?? config("countries.{$attributes['country']}.currency")
                ?? $profile?->currency
                ?? 'GBP',
        );

        $property = DB::transaction(function () use ($attributes, $featureNames) {
            $property = Property::create($attributes);
            $this->syncFeatures($property, $featureNames);

            return $property;
        });

        return response()->json(['data' => $this->includeFeatureNames($property->fresh('features'))], 201);
    }

    public function update(Request $request, int $property): JsonResponse
    {
        $record = $this->find($request, $property);
        $attributes = $request->validate($this->rules($request, true));
        foreach (['country', 'currency'] as $regionalField) {
            if (isset($attributes[$regionalField])) {
                $attributes[$regionalField] = strtoupper($attributes[$regionalField]);
            }
        }
        $hasFeatures = array_key_exists('feature_names', $attributes);
        $featureNames = $attributes['feature_names'] ?? [];
        unset($attributes['feature_names']);
        DB::transaction(function () use ($record, $attributes, $hasFeatures, $featureNames) {
            $record->update($attributes);
            if ($hasFeatures) {
                $this->syncFeatures($record, $featureNames);
            }
        });

        return response()->json(['data' => $this->includeFeatureNames($record->fresh('features'))]);
    }

    public function destroy(Request $request, int $property): JsonResponse
    {
        $this->find($request, $property)->delete();

        return response()->json(null, 204);
    }

    private function find(Request $request, int $property): Property
    {
        return Property::query()
            ->where('team_id', $this->teamId($request))
            ->findOrFail($property);
    }

    private function rules(Request $request, bool $updating = false): array
    {
        $teamId = $this->teamId($request);
        $required = $updating ? 'sometimes' : 'required';
        $profile = OrganisationProfile::where('team_id', $teamId)->first();
        $operatingCountries = $profile?->operating_countries ?: array_keys(config('countries', []));
        $currencies = collect(config('countries', []))->pluck('currency')->unique()->values()->all();

        return [
            'title' => [$required, 'string', 'max:255'],
            'description' => [$required, 'string'],
            'internal_notes' => ['nullable', 'string'],
            'location' => [$required, 'string', 'max:255'],
            'structured_address' => ['nullable', 'array'],
            'structured_address.line_1' => ['required_with:structured_address', 'string', 'max:255'],
            'structured_address.line_2' => ['nullable', 'string', 'max:255'],
            'structured_address.city' => ['required_with:structured_address', 'string', 'max:100'],
            'structured_address.region' => ['nullable', 'string', 'max:100'],
            'structured_address.postal_code' => ['nullable', 'string', 'max:30'],
            'structured_address.country' => ['nullable', 'string', 'size:2', Rule::in($operatingCountries)],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'country' => ['nullable', 'string', 'size:2', Rule::in($operatingCountries)],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'price' => [$required, 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3', Rule::in($currencies)],
            'bedrooms' => [$required, 'integer', 'min:0'],
            'bathrooms' => [$required, 'integer', 'min:0'],
            'reception_rooms' => ['sometimes', 'integer', 'between:0,100'],
            'parking' => ['nullable', 'array'],
            'parking.spaces' => ['nullable', 'integer', 'between:0,1000'],
            'parking.types' => ['nullable', 'array', 'max:20'],
            'parking.types.*' => ['string', 'distinct', Rule::in(['garage', 'driveway', 'allocated', 'street', 'carport', 'underground', 'communal'])],
            'parking.notes' => ['nullable', 'string', 'max:1000'],
            'gardens' => ['nullable', 'array'],
            'gardens.front' => ['sometimes', 'boolean'],
            'gardens.rear' => ['sometimes', 'boolean'],
            'gardens.communal' => ['sometimes', 'boolean'],
            'gardens.size' => ['nullable', 'string', 'max:100'],
            'gardens.orientation' => ['nullable', Rule::in(['north', 'north_east', 'east', 'south_east', 'south', 'south_west', 'west', 'north_west'])],
            'area_sqft' => [$required, 'numeric', 'min:0'],
            'year_built' => array_merge([$required], Property::yearBuiltRules()),
            'property_type' => [$required, Rule::in(['residential', 'commercial', 'land', 'new_build', 'development', 'mixed_use', 'house', 'apartment'])],
            'status' => ['sometimes', Rule::in(['draft', 'coming_soon', 'available', 'under_offer', 'sstc', 'exchanged', 'sold', 'to_let', 'let_agreed', 'let', 'withdrawn', 'archived', 'pending', 'approved', 'rejected'])],
            'list_date' => ['nullable', 'date'],
            'sold_date' => ['nullable', 'date'],
            'agent_id' => ['nullable', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'branch_id' => ['nullable', Rule::exists('branches', 'id')->where('team_id', $teamId)],
            'virtual_tour_url' => ['nullable', 'url', 'max:2048'],
            'is_featured' => ['sometimes', 'boolean'],
            'energy_rating' => ['nullable', 'string', 'max:10'],
            'epc' => ['nullable', 'array'],
            'epc.rating' => ['nullable', 'string', 'max:10'],
            'epc.score' => ['nullable', 'integer', 'between:0,100'],
            'epc.assessment_date' => ['nullable', 'date'],
            'epc.expiry_date' => ['nullable', 'date', 'after_or_equal:epc.assessment_date'],
            'epc.certificate_reference' => ['nullable', 'string', 'max:100'],
            'feature_names' => ['nullable', 'array', 'max:100'],
            'feature_names.*' => ['string', 'distinct', 'max:100'],
        ];
    }

    private function syncFeatures(Property $property, array $featureNames): void
    {
        $property->features()->delete();
        $property->features()->createMany(collect($featureNames)
            ->map(fn (string $name) => [
                'team_id' => $property->team_id,
                'feature_name' => $name,
            ])->all());
    }

    private function includeFeatureNames(Property $property): Property
    {
        $property->loadMissing('features');
        $property->setAttribute('feature_names', $property->features->pluck('feature_name')->values()->all());

        return $property;
    }

    private function teamId(Request $request): int
    {
        $user = $request->user();
        $teamId = $user->current_team_id;

        if (! $teamId || ! $user->allTeams()->contains('id', $teamId)) {
            throw ValidationException::withMessages([
                'team' => ['Select an organisation you belong to first.'],
            ]);
        }

        return (int) $teamId;
    }
}
