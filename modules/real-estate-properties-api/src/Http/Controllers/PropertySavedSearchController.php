<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\RealEstate\Properties\Application\DeletePropertySearch;
use Liberu\RealEstate\Properties\Application\SavePropertySearch;
use Liberu\RealEstate\Properties\Models\PropertySavedSearch;
use Liberu\RealEstate\PropertiesApi\Http\Resources\PropertySavedSearchResource;

final class PropertySavedSearchController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $pageSize = max(1, min($request->integer('page_size', 12), 100));

        return PropertySavedSearchResource::collection(PropertySavedSearch::query()->forUser($user->current_team_id, $user->getAuthIdentifier())->latest()->paginate($pageSize)->withQueryString())->response();
    }

    public function store(Request $request, SavePropertySearch $save): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'criteria' => ['required', 'array'], 'criteria.search' => ['sometimes', 'nullable', 'string', 'max:255'], 'criteria.minPrice' => ['sometimes', 'nullable', 'numeric', 'min:0'], 'criteria.maxPrice' => ['sometimes', 'nullable', 'numeric', 'min:0'], 'criteria.propertyType' => ['sometimes', 'nullable', 'string', 'max:40'], 'criteria.status' => ['sometimes', 'nullable', 'string', 'max:40'], 'criteria.selectedAmenities' => ['sometimes', 'array', 'max:20']]);

        return (new PropertySavedSearchResource($save->handle($user->current_team_id, $user->getAuthIdentifier(), $data['name'], $data['criteria'])))->response()->setStatusCode(201);
    }

    public function destroy(Request $request, int $savedSearch, DeletePropertySearch $delete): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);

        return response()->json(['deleted' => $delete->handle($user->current_team_id, $user->getAuthIdentifier(), $savedSearch)]);
    }
}
