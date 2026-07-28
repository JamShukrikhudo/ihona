<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        return response()->json(
            $query->latest('id')->paginate(min(max($request->integer('per_page', 20), 1), 100))
        );
    }

    public function show(Request $request, int $property): JsonResponse
    {
        $record = Property::query()
            ->where('team_id', $this->teamId($request))
            ->with(['images', 'features', 'rooms', 'bookings'])
            ->findOrFail($property);

        return response()->json(['data' => $record]);
    }

    public function store(Request $request): JsonResponse
    {
        $attributes = $request->validate($this->rules($request));
        $attributes['team_id'] = $this->teamId($request);
        $attributes['user_id'] = $request->user()->id;
        $attributes['agent_id'] ??= $request->user()->id;
        $attributes['status'] ??= 'draft';
        $attributes['list_date'] ??= now()->toDateString();

        $property = Property::create($attributes);

        return response()->json(['data' => $property->fresh()], 201);
    }

    public function update(Request $request, int $property): JsonResponse
    {
        $record = $this->find($request, $property);
        $record->update($request->validate($this->rules($request, true)));

        return response()->json(['data' => $record->fresh()]);
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

        return [
            'title' => [$required, 'string', 'max:255'],
            'description' => [$required, 'string'],
            'location' => [$required, 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'country' => ['nullable', 'string', 'size:2'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'price' => [$required, 'numeric', 'min:0'],
            'bedrooms' => [$required, 'integer', 'min:0'],
            'bathrooms' => [$required, 'integer', 'min:0'],
            'area_sqft' => [$required, 'numeric', 'min:0'],
            'year_built' => [$required, 'integer', 'min:1000', 'max:'.(now()->year + 10)],
            'property_type' => [$required, Rule::in(['residential', 'commercial', 'land', 'new_build', 'development', 'mixed_use', 'house', 'apartment'])],
            'status' => ['sometimes', Rule::in(['draft', 'coming_soon', 'available', 'under_offer', 'sstc', 'exchanged', 'sold', 'to_let', 'let_agreed', 'let', 'withdrawn', 'archived', 'pending', 'approved', 'rejected'])],
            'list_date' => ['nullable', 'date'],
            'sold_date' => ['nullable', 'date'],
            'agent_id' => ['nullable', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'virtual_tour_url' => ['nullable', 'url', 'max:2048'],
            'is_featured' => ['sometimes', 'boolean'],
            'energy_rating' => ['nullable', 'string', 'max:10'],
        ];
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
