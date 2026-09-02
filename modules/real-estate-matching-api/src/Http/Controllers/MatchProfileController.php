<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MatchingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Matching\Application\CalculateMatchScore;
use Liberu\RealEstate\Matching\Application\CreateMatchProfile;
use Liberu\RealEstate\Matching\Application\DeleteMatchProfile;
use Liberu\RealEstate\Matching\Application\RankPropertyRecommendations;
use Liberu\RealEstate\Matching\Application\UpdateMatchProfile;
use Liberu\RealEstate\Matching\Application\UpdateMatchProfileSection;
use Liberu\RealEstate\Matching\Domain\MatchProfileSection;
use Liberu\RealEstate\Matching\Models\MatchProfile;
use Liberu\RealEstate\MatchingApi\Http\Resources\MatchProfileResource;
use Liberu\RealEstate\MatchingApi\Http\Resources\MatchScoreResource;

final class MatchProfileController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $size = max(1, min($request->integer('page_size', 25), 100));

        return MatchProfileResource::collection(MatchProfile::query()->forTeam($teamId)->latest()->paginate($size))->response();
    }

    public function store(Request $request, CreateMatchProfile $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['subject' => ['required', 'string', 'max:255'], 'party_id' => ['nullable', 'integer'], 'score' => ['sometimes', 'integer', 'min:0', 'max:100'], 'requirements' => ['sometimes', 'array'], 'affordability' => ['sometimes', 'array'], 'preferences' => ['sometimes', 'array'], 'alerts' => ['sometimes', 'array'], 'feedback' => ['sometimes', 'array'], 'exclusions' => ['sometimes', 'array']]);

        return (new MatchProfileResource($create->handle($user->current_team_id, $user->getAuthIdentifier(), $data)))->response()->setStatusCode(201);
    }

    public function show(Request $request, MatchProfile $matchProfile): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $matchProfile->team_id, 404);

        return (new MatchProfileResource($matchProfile))->response();
    }

    public function update(Request $request, MatchProfile $matchProfile, UpdateMatchProfile $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $matchProfile->team_id, 404);
        $data = $request->validate(['subject' => ['sometimes', 'string', 'max:255'], 'score' => ['sometimes', 'integer', 'min:0', 'max:100'], 'requirements' => ['sometimes', 'array'], 'affordability' => ['sometimes', 'array'], 'preferences' => ['sometimes', 'array'], 'alerts' => ['sometimes', 'array'], 'feedback' => ['sometimes', 'array'], 'exclusions' => ['sometimes', 'array']]);

        return (new MatchProfileResource($update->handle($matchProfile, $teamId, $data)))->response();
    }

    public function updateSection(Request $request, MatchProfile $matchProfile, string $section, UpdateMatchProfileSection $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $matchProfile->team_id, 404);
        $sectionEnum = MatchProfileSection::from($section);
        $data = $request->validate($sectionEnum === MatchProfileSection::Scoring
            ? ['score' => ['required', 'integer', 'between:0,100']]
            : ['value' => ['required', 'array']]);

        return (new MatchProfileResource($update->handle($matchProfile, $teamId, $sectionEnum, $sectionEnum === MatchProfileSection::Scoring ? ['score' => $data['score']] : $data['value'])))->response();
    }

    public function calculateScore(Request $request, CalculateMatchScore $calculate): JsonResponse
    {
        $data = $request->validate(['criteria' => ['required', 'array'], 'property' => ['required', 'array'], 'property.price' => ['required', 'numeric', 'min:0']]);

        return (new MatchScoreResource($calculate->handle($data['criteria'], $data['property'])))->response();
    }

    public function destroy(Request $request, MatchProfile $matchProfile, DeleteMatchProfile $delete): Response
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $matchProfile->team_id, 404);
        $delete->handle($matchProfile, $teamId);

        return response()->noContent();
    }
}
