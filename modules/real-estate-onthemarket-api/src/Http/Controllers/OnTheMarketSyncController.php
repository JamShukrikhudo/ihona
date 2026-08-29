<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OnTheMarketApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\OnTheMarket\Application\CreateOnTheMarketSync;
use Liberu\RealEstate\OnTheMarket\Application\DeleteOnTheMarketSync;
use Liberu\RealEstate\OnTheMarket\Application\SyncOnTheMarketListing;
use Liberu\RealEstate\OnTheMarket\Application\UpdateOnTheMarketSync;
use Liberu\RealEstate\OnTheMarket\Models\OnTheMarketSync;
use Liberu\RealEstate\OnTheMarketApi\Http\Resources\OnTheMarketSyncResource;

final class OnTheMarketSyncController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return OnTheMarketSyncResource::collection(OnTheMarketSync::query()->forTeam($teamId)->latest()->paginate(max(1, min($request->integer('page_size', 25), 100))))->response();
    }

    public function store(Request $request, CreateOnTheMarketSync $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['listing_id' => ['required', 'integer'], 'property_id' => ['nullable', 'integer'], 'external_id' => ['nullable', 'string', 'max:255'], 'status' => ['sometimes', 'string', 'in:pending,syncing,synced,failed,disabled'], 'payload' => ['sometimes', 'array'], 'last_synced_at' => ['nullable', 'date'], 'error' => ['nullable', 'string']]);

        return (new OnTheMarketSyncResource($create->handle($user->current_team_id, $user->getAuthIdentifier(), $data)))->response()->setStatusCode(201);
    }

    public function show(Request $request, OnTheMarketSync $onTheMarketSync): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $onTheMarketSync->team_id, 404);

        return (new OnTheMarketSyncResource($onTheMarketSync))->response();
    }

    public function sync(Request $request, OnTheMarketSync $onTheMarketSync, SyncOnTheMarketListing $sync): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $onTheMarketSync->team_id, 404);
        $data = $request->validate(['reference' => ['required', 'string', 'max:255'], 'payload' => ['required', 'array']]);

        return (new OnTheMarketSyncResource($sync->handle($onTheMarketSync, $data['reference'], $data['payload'], ['certificate' => config('onthemarket.certificate'), 'key' => config('onthemarket.key'), 'key_password' => config('onthemarket.key_password')])))->response();
    }

    public function update(Request $request, OnTheMarketSync $onTheMarketSync, UpdateOnTheMarketSync $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $onTheMarketSync->team_id, 404);
        $data = $request->validate(['external_id' => ['sometimes', 'nullable', 'string', 'max:255'], 'payload' => ['sometimes', 'array'], 'last_synced_at' => ['nullable', 'date'], 'error' => ['nullable', 'string']]);

        return (new OnTheMarketSyncResource($update->handle($onTheMarketSync, $teamId, $data)))->response();
    }

    public function destroy(Request $request, OnTheMarketSync $onTheMarketSync, DeleteOnTheMarketSync $delete): Response
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $onTheMarketSync->team_id, 404);
        $delete->handle($onTheMarketSync, $teamId);

        return response()->noContent();
    }
}
