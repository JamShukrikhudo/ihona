<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicWebsiteController extends Controller
{
    private const PUBLIC_STATUSES = [
        'available', 'under_offer', 'sstc', 'exchanged', 'sold',
        'to_let', 'let_agreed', 'let', 'approved', 'For Sale', 'For Rent', 'Sold', 'Rented',
    ];

    public function properties(Request $request, Team $team): JsonResponse
    {
        $query = $this->publicProperties($team)
            ->with(['images', 'features'])
            ->when($request->boolean('featured'), fn (Builder $query) => $query->where('is_featured', true))
            ->when($request->boolean('new'), fn (Builder $query) => $query->where('list_date', '>=', now()->subDays(30)))
            ->when($request->boolean('sold'), fn (Builder $query) => $query->whereIn('status', ['sold', 'let', 'Sold', 'Rented']))
            ->when($request->filled('property_type'), fn (Builder $query) => $query->where('property_type', $request->string('property_type')))
            ->when($request->filled('min_price'), fn (Builder $query) => $query->where('price', '>=', $request->input('min_price')))
            ->when($request->filled('max_price'), fn (Builder $query) => $query->where('price', '<=', $request->input('max_price')))
            ->when($request->filled('bedrooms'), fn (Builder $query) => $query->where('bedrooms', '>=', $request->integer('bedrooms')))
            ->when($request->filled('search'), function (Builder $query) use ($request) {
                $term = '%'.$request->string('search')->trim().'%';
                $query->where(fn (Builder $query) => $query
                    ->where('title', 'like', $term)
                    ->orWhere('location', 'like', $term)
                    ->orWhere('postal_code', 'like', $term));
            });

        return response()->json($query->latest('list_date')->paginate(
            min(max($request->integer('per_page', 20), 1), 100)
        ));
    }

    public function property(Team $team, int $property): JsonResponse
    {
        $record = $this->publicProperties($team)
            ->with(['images', 'features', 'rooms'])
            ->findOrFail($property);

        return response()->json(['data' => $record]);
    }

    public function branches(Team $team): JsonResponse
    {
        return response()->json(['data' => $team->branches()
            ->select(['id', 'team_id', 'name', 'address', 'phone', 'phone_number', 'email'])
            ->orderBy('name')
            ->get()]);
    }

    public function staff(Team $team): JsonResponse
    {
        $staff = $team->allUsers()
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'profile_photo_url' => $user->profile_photo_url,
            ])
            ->values();

        return response()->json(['data' => $staff]);
    }

    private function publicProperties(Team $team): Builder
    {
        return Property::query()
            ->where('team_id', $team->id)
            ->whereIn('status', self::PUBLIC_STATUSES);
    }
}
