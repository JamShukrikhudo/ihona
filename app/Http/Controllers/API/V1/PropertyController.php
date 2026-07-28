<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
