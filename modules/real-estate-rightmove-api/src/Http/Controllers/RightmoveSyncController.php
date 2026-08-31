<?php

declare(strict_types=1);

namespace Liberu\RealEstate\RightmoveApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Rightmove\Application\CreateRightmoveSync;
use Liberu\RealEstate\Rightmove\Application\DeleteRightmoveSync;
use Liberu\RealEstate\Rightmove\Application\SyncRightmoveListing;
use Liberu\RealEstate\Rightmove\Application\UpdateRightmoveSync;
use Liberu\RealEstate\Rightmove\Models\RightmoveSync;
use Liberu\RealEstate\RightmoveApi\Http\Resources\RightmoveSyncResource;

final class RightmoveSyncController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return RightmoveSyncResource::collection(RightmoveSync::query()->forTeam($teamId)->latest()->paginate(max(1, min($request->integer('page_size', 25), 100))))->response();
    }

    public function store(Request $request, CreateRightmoveSync $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['listing_id' => ['required', 'integer'], 'property_id' => ['nullable', 'integer'], 'external_id' => ['nullable', 'string', 'max:255'], 'status' => ['sometimes', 'string', 'in:pending,syncing,synced,failed,disabled'], 'payload' => ['sometimes', 'array'], 'last_synced_at' => ['nullable', 'date'], 'error' => ['nullable', 'string']]);

        return (new RightmoveSyncResource($create->handle($user->current_team_id, $user->getAuthIdentifier(), $data)))->response()->setStatusCode(201);
    }

    public function show(Request $request, RightmoveSync $rightmoveSync): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $rightmoveSync->team_id, 404);

        return (new RightmoveSyncResource($rightmoveSync))->response();
    }

    public function sync(Request $request, RightmoveSync $rightmoveSync, SyncRightmoveListing $sync): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $rightmoveSync->team_id, 404);
        $data = $request->validate(['reference' => ['required', 'string', 'max:255'], 'payload' => ['required', 'array']]);

        return (new RightmoveSyncResource($sync->handle($rightmoveSync, $data['reference'], $data['payload'], ['client_id' => config('rightmove.client_id'), 'client_secret' => config('rightmove.client_secret')])))->response();
    }

    public function update(Request $request, RightmoveSync $rightmoveSync, UpdateRightmoveSync $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $rightmoveSync->team_id, 404);
        $data = $request->validate(['external_id' => ['sometimes', 'nullable', 'string', 'max:255'], 'payload' => ['sometimes', 'array'], 'last_synced_at' => ['nullable', 'date'], 'error' => ['nullable', 'string']]);

        return (new RightmoveSyncResource($update->handle($rightmoveSync, $teamId, $data)))->response();
    }

    public function destroy(Request $request, RightmoveSync $rightmoveSync, DeleteRightmoveSync $delete): Response
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $rightmoveSync->team_id, 404);
        $delete->handle($rightmoveSync, $teamId);

        return response()->noContent();
    }
}
