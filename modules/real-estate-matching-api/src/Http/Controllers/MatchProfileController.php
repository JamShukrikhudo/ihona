<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MatchingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Matching\Application\CreateMatchProfile;
use Liberu\RealEstate\Matching\Application\DeleteMatchProfile;
use Liberu\RealEstate\Matching\Application\UpdateMatchProfile;
use Liberu\RealEstate\Matching\Models\MatchProfile;

final class MatchProfileController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $size = max(1, min($request->integer('page_size', 25), 100));

        return response()->json(['data' => MatchProfile::query()->forTeam($teamId)->latest()->paginate($size)]);
    }

    public function store(Request $request, CreateMatchProfile $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['subject' => ['required', 'string', 'max:255'], 'party_id' => ['nullable', 'integer'], 'score' => ['sometimes', 'integer', 'min:0', 'max:100'], 'requirements' => ['sometimes', 'array'], 'affordability' => ['sometimes', 'array'], 'preferences' => ['sometimes', 'array'], 'alerts' => ['sometimes', 'array'], 'feedback' => ['sometimes', 'array'], 'exclusions' => ['sometimes', 'array']]);

        return response()->json(['data' => $create->handle($user->current_team_id, $user->getAuthIdentifier(), $data)], 201);
    }

    public function show(Request $request, MatchProfile $matchProfile): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $matchProfile->team_id, 404);

        return response()->json(['data' => $matchProfile]);
    }

    public function update(Request $request, MatchProfile $matchProfile, UpdateMatchProfile $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $matchProfile->team_id, 404);
        $data = $request->validate(['subject' => ['sometimes', 'string', 'max:255'], 'score' => ['sometimes', 'integer', 'min:0', 'max:100'], 'requirements' => ['sometimes', 'array'], 'affordability' => ['sometimes', 'array'], 'preferences' => ['sometimes', 'array'], 'alerts' => ['sometimes', 'array'], 'feedback' => ['sometimes', 'array'], 'exclusions' => ['sometimes', 'array']]);

        return response()->json(['data' => $update->handle($matchProfile, $teamId, $data)]);
    }

    public function destroy(Request $request, MatchProfile $matchProfile, DeleteMatchProfile $delete): Response
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $matchProfile->team_id, 404);
        $delete->handle($matchProfile, $teamId);

        return response()->noContent();
    }
}
