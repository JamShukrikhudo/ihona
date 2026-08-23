<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Properties\Application\CreateProperty;
use Liberu\RealEstate\Properties\Application\DeleteProperty;
use Liberu\RealEstate\Properties\Application\UpdateProperty;
use Liberu\RealEstate\Properties\Models\Property;

final class PropertyController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        $pageSize = max(1, min($request->integer('page_size', 25), 100));

        return response()->json(['data' => Property::query()->forTeam($teamId)->latest()->paginate($pageSize)]);
    }

    public function store(Request $request, CreateProperty $create): JsonResponse
    {
        $validated = $request->validate([
            'address' => ['required', 'string', 'max:500'],
            'property_type' => ['sometimes', 'string', 'max:40'],
            'characteristics' => ['sometimes', 'array'],
            'utilities' => ['sometimes', 'array'],
            'features' => ['sometimes', 'array'],
        ]);
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);

        $property = $create->handle($user->current_team_id, $user->getAuthIdentifier(), $validated);

        return response()->json(['data' => $property], 201);
    }

    public function show(Request $request, Property $property): JsonResponse
    {
        abort_unless($request->user()?->current_team_id === $property->team_id, 404);

        return response()->json(['data' => $property->load('history')]);
    }

    public function update(Request $request, Property $property, UpdateProperty $update): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id === $property->team_id, 404);

        $validated = $request->validate([
            'address' => ['sometimes', 'string', 'max:500'],
            'property_type' => ['sometimes', 'string', 'max:40'],
            'characteristics' => ['sometimes', 'array'],
            'utilities' => ['sometimes', 'array'],
            'features' => ['sometimes', 'array'],
        ]);

        return response()->json([
            'data' => $update->handle($property->team_id, $user->getAuthIdentifier(), $property->getKey(), $validated),
        ]);
    }

    public function destroy(Request $request, Property $property, DeleteProperty $delete): Response
    {
        $user = $request->user();
        abort_unless($user?->current_team_id === $property->team_id, 404);

        $delete->handle($property->team_id, $user->getAuthIdentifier(), $property->getKey());

        return response()->noContent();
    }
}
