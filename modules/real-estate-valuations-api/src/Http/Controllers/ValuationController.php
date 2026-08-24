<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ValuationsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Valuations\Application\CalculateComparables;
use Liberu\RealEstate\Valuations\Application\CompleteValuation;
use Liberu\RealEstate\Valuations\Application\ConvertValuation;
use Liberu\RealEstate\Valuations\Application\CreateValuation;
use Liberu\RealEstate\Valuations\Application\DeleteValuation;
use Liberu\RealEstate\Valuations\Application\ScheduleValuation;
use Liberu\RealEstate\Valuations\Application\UpdateValuation;
use Liberu\RealEstate\Valuations\Models\Valuation;

final class ValuationController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $size = max(1, min($request->integer('page_size', 25), 100));

        return response()->json(['data' => Valuation::query()->forTeam($teamId)->latest()->paginate($size)]);
    }

    public function store(Request $request, CreateValuation $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['subject' => ['required', 'string', 'max:255'], 'property_id' => ['nullable', 'integer'], 'party_id' => ['nullable', 'integer'], 'valued_amount' => ['nullable', 'numeric', 'min:0'], 'fee_amount' => ['nullable', 'numeric', 'min:0'], 'currency' => ['nullable', 'string', 'size:3'], 'comparable_data' => ['sometimes', 'array'], 'recommendation' => ['sometimes', 'array'], 'scheduled_at' => ['nullable', 'date'], 'follow_up_at' => ['nullable', 'date']]);

        return response()->json(['data' => $create->handle($user->current_team_id, $user->getAuthIdentifier(), $data)], 201);
    }

    public function show(Request $request, Valuation $valuation): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $valuation->team_id, 404);

        return response()->json(['data' => $valuation]);
    }

    public function update(Request $request, Valuation $valuation, UpdateValuation $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $valuation->team_id, 404);
        $data = $request->validate(['subject' => ['sometimes', 'string', 'max:255'], 'valued_amount' => ['nullable', 'numeric', 'min:0'], 'fee_amount' => ['nullable', 'numeric', 'min:0'], 'currency' => ['nullable', 'string', 'size:3'], 'comparable_data' => ['sometimes', 'array'], 'recommendation' => ['sometimes', 'array'], 'scheduled_at' => ['nullable', 'date'], 'follow_up_at' => ['nullable', 'date'], 'status' => ['sometimes', 'string', 'in:draft,scheduled,completed,converted,cancelled']]);

        return response()->json(['data' => $update->handle($valuation, $teamId, $data)]);
    }

    public function destroy(Request $request, Valuation $valuation, DeleteValuation $delete): Response
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $valuation->team_id, 404);
        $delete->handle($valuation, $teamId);

        return response()->noContent();
    }

    public function schedule(Request $request, Valuation $valuation, ScheduleValuation $schedule): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $valuation->team_id, 404);

        return response()->json(['data' => $schedule->handle($valuation, $teamId, (string) $request->validate(['scheduled_at' => ['required', 'date', 'after:now']])['scheduled_at'])]);
    }

    public function complete(Request $request, Valuation $valuation, CompleteValuation $complete): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $valuation->team_id, 404);

        return response()->json(['data' => $complete->handle($valuation, $teamId, $request->validate(['valued_amount' => ['required', 'numeric', 'min:0'], 'recommendation' => ['sometimes', 'array'], 'follow_up_at' => ['nullable', 'date', 'after:now']]))]);
    }

    public function convert(Request $request, Valuation $valuation, ConvertValuation $convert): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $valuation->team_id, 404);

        return response()->json(['data' => $convert->handle($valuation, $teamId, $request->validate(['type' => ['required', 'string', 'max:80'], 'id' => ['nullable', 'integer'], 'metadata' => ['sometimes', 'array']]))]);
    }

    public function comparables(Request $request, Valuation $valuation, CalculateComparables $calculate): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $valuation->team_id, 404);

        return response()->json(['data' => $calculate->handle($valuation, $teamId, $request->validate(['comparables' => ['required', 'array', 'min:1'], 'comparables.*.amount' => ['required', 'numeric', 'min:0']])['comparables'])]);
    }
}
