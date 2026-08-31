<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Liberu\Foundation\Organizations\Models\Team;
use Liberu\RealEstate\Properties\Domain\PropertyStatus;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\PropertiesApi\Http\Resources\PublicPropertyResource;

/**
 * Anonymous, read-only property browsing for the public storefront
 * (ihona-frontend). See PublicTerritoryController (real-estate-core-api)
 * for why this resolves "the" public team rather than scoping by
 * $request->user() — there isn't one.
 */
final class PublicPropertyController
{
    public function index(Request $request): JsonResponse
    {
        $team = Team::query()->oldest()->first();

        if (! $team) {
            return PublicPropertyResource::collection(collect())->response();
        }

        $filters = $request->validate([
            'territory' => ['sometimes', 'nullable', 'string', 'max:20'],
            'type' => ['sometimes', 'nullable', 'string', 'max:40'],
            'deal_type' => ['sometimes', 'nullable', Rule::in(['sale', 'rent'])],
            'min_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'max_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ]);

        $properties = Property::query()
            ->with('territory')
            ->forTeam($team->id)
            ->where('status', PropertyStatus::Available->value)
            ->when($filters['territory'] ?? null, fn ($q, $code) => $q->whereHas('territory', fn ($t) => $t->where('code', strtoupper($code))))
            ->when($filters['type'] ?? null, fn ($q, $type) => $q->where('property_type', $type))
            ->when($filters['deal_type'] ?? null, fn ($q, $dealType) => $q->where('deal_type', $dealType))
            ->when($filters['min_price'] ?? null, fn ($q, $min) => $q->where('price', '>=', $min))
            ->when($filters['max_price'] ?? null, fn ($q, $max) => $q->where('price', '<=', $max))
            ->latest()
            ->paginate(max(1, min($request->integer('page_size', 24), 60)));

        return PublicPropertyResource::collection($properties)->response();
    }

    public function show(Property $property): JsonResponse
    {
        abort_unless($property->status === PropertyStatus::Available, 404);

        return (new PublicPropertyResource($property->load('territory')))->response();
    }
}
