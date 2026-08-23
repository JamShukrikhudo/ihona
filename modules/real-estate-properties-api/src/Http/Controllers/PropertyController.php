<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\RealEstate\Properties\Application\CreateProperty;
use Liberu\RealEstate\Properties\Models\Property;

final class PropertyController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return response()->json([
            'data' => Property::query()->forTeam($teamId)->latest()->paginate(min((int) $request->integer('page_size', 25), 100)),
        ]);
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
}
