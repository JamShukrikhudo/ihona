<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MarketingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Marketing\Application\CreateMarketingCampaign;
use Liberu\RealEstate\Marketing\Application\DeleteMarketingCampaign;
use Liberu\RealEstate\Marketing\Application\UpdateMarketingCampaign;
use Liberu\RealEstate\Marketing\Models\MarketingCampaign;

final class MarketingCampaignController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $size = max(1, min($request->integer('page_size', 25), 100));

        return response()->json(['data' => MarketingCampaign::query()->forTeam($teamId)->latest()->paginate($size)]);
    }

    public function store(Request $request, CreateMarketingCampaign $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'channel' => ['required', 'string', 'max:80'], 'property_id' => ['nullable', 'integer'], 'listing_id' => ['nullable', 'integer'], 'status' => ['sometimes', 'string', 'in:draft,scheduled,active,paused,completed,cancelled'], 'audience' => ['sometimes', 'array'], 'content' => ['sometimes', 'array'], 'schedule' => ['sometimes', 'array'], 'metrics' => ['sometimes', 'array'], 'notes' => ['nullable', 'string']]);

        return response()->json(['data' => $create->handle($user->current_team_id, $user->getAuthIdentifier(), $data)], 201);
    }

    public function show(Request $request, MarketingCampaign $marketingCampaign): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $marketingCampaign->team_id, 404);

        return response()->json(['data' => $marketingCampaign]);
    }

    public function update(Request $request, MarketingCampaign $marketingCampaign, UpdateMarketingCampaign $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $marketingCampaign->team_id, 404);
        $data = $request->validate(['name' => ['sometimes', 'string', 'max:255'], 'channel' => ['sometimes', 'string', 'max:80'], 'status' => ['sometimes', 'string', 'in:draft,scheduled,active,paused,completed,cancelled'], 'audience' => ['sometimes', 'array'], 'content' => ['sometimes', 'array'], 'schedule' => ['sometimes', 'array'], 'metrics' => ['sometimes', 'array'], 'notes' => ['nullable', 'string']]);

        return response()->json(['data' => $update->handle($marketingCampaign, $teamId, $data)]);
    }

    public function destroy(Request $request, MarketingCampaign $marketingCampaign, DeleteMarketingCampaign $delete): Response
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $marketingCampaign->team_id, 404);
        $delete->handle($marketingCampaign, $teamId);

        return response()->noContent();
    }
}
