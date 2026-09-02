<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\RealEstate\Properties\Models\CommunityEvent;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\PropertiesApi\Http\Resources\CommunityEventResource;

final class CommunityEventController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $data = $request->validate(['category' => ['sometimes', 'nullable', 'string', 'max:80'], 'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'], 'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'], 'radius' => ['sometimes', 'nullable', 'numeric', 'gt:0', 'max:500'], 'property_id' => ['sometimes', 'nullable', 'integer']]);
        $query = CommunityEvent::query()->forTeam($teamId)->public()->upcoming()->category($data['category'] ?? null);

        if (($data['property_id'] ?? null) !== null) {
            $property = Property::query()->forTeam($teamId)->whereKey($data['property_id'])->firstOrFail();
            $data['latitude'] ??= $property->latitude;
            $data['longitude'] ??= $property->longitude;
        }

        if (($data['latitude'] ?? null) !== null && ($data['longitude'] ?? null) !== null) {
            $query->nearby($data['latitude'], $data['longitude'], $data['radius'] ?? 10);
        }

        return CommunityEventResource::collection($query->paginate(20)->withQueryString())->response();
    }

    public function show(Request $request, CommunityEvent $event): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($event->is_public || ($teamId !== null && (string) $event->team_id === (string) $teamId), 403);
        abort_unless($event->team_id === null || (string) $event->team_id === (string) $teamId, 404);

        return (new CommunityEventResource($event))->response();
    }
}
