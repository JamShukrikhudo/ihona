<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ZooplaApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Zoopla\Application\CreateZooplaSync;
use Liberu\RealEstate\Zoopla\Application\DeleteZooplaSync;
use Liberu\RealEstate\Zoopla\Application\SyncZooplaListing;
use Liberu\RealEstate\Zoopla\Application\UpdateZooplaSync;
use Liberu\RealEstate\Zoopla\Models\ZooplaSync;

final class ZooplaSyncController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return response()->json(['data' => ZooplaSync::query()->forTeam($teamId)->latest()->paginate(max(1, min($request->integer('page_size', 25), 100)))]);
    }

    public function store(Request $request, CreateZooplaSync $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['listing_id' => ['required', 'integer'], 'property_id' => ['nullable', 'integer'], 'external_id' => ['nullable', 'string', 'max:255'], 'status' => ['sometimes', 'string', 'in:pending,syncing,synced,failed,disabled'], 'payload' => ['sometimes', 'array'], 'last_synced_at' => ['nullable', 'date'], 'error' => ['nullable', 'string']]);

        return response()->json(['data' => $create->handle($user->current_team_id, $user->getAuthIdentifier(), $data)], 201);
    }

    public function show(Request $request, ZooplaSync $zooplaSync): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $zooplaSync->team_id, 404);

        return response()->json(['data' => $zooplaSync]);
    }

    public function sync(Request $request, ZooplaSync $zooplaSync, SyncZooplaListing $sync): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $zooplaSync->team_id, 404);
        $data = $request->validate(['reference' => ['required', 'string', 'max:255'], 'payload' => ['required', 'array']]);

        return response()->json(['data' => $sync->handle($zooplaSync, $data['reference'], $data['payload'], ['certificate' => config('zoopla.certificate'), 'key' => config('zoopla.key'), 'key_password' => config('zoopla.key_password')])]);
    }

    public function update(Request $request, ZooplaSync $zooplaSync, UpdateZooplaSync $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $zooplaSync->team_id, 404);
        $data = $request->validate(['external_id' => ['sometimes', 'nullable', 'string', 'max:255'], 'status' => ['sometimes', 'string', 'in:pending,syncing,synced,failed,disabled'], 'payload' => ['sometimes', 'array'], 'last_synced_at' => ['nullable', 'date'], 'error' => ['nullable', 'string']]);

        return response()->json(['data' => $update->handle($zooplaSync, $teamId, $data)]);
    }

    public function destroy(Request $request, ZooplaSync $zooplaSync, DeleteZooplaSync $delete): Response
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $zooplaSync->team_id, 404);
        $delete->handle($zooplaSync, $teamId);

        return response()->noContent();
    }
}
