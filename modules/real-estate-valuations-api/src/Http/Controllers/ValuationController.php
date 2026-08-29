<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ValuationsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Valuations\Application\CalculateComparables;
use Liberu\RealEstate\Valuations\Application\CalculateHomeValuation;
use Liberu\RealEstate\Valuations\Application\CompleteValuation;
use Liberu\RealEstate\Valuations\Application\ConvertValuation;
use Liberu\RealEstate\Valuations\Application\CreateValuation;
use Liberu\RealEstate\Valuations\Application\DeleteValuation;
use Liberu\RealEstate\Valuations\Application\ScheduleValuation;
use Liberu\RealEstate\Valuations\Application\UpdateValuation;
use Liberu\RealEstate\Valuations\Models\Valuation;
use Liberu\RealEstate\ValuationsApi\Http\Resources\ValuationCalculationResource;
use Liberu\RealEstate\ValuationsApi\Http\Resources\ValuationResource;

final class ValuationController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $size = max(1, min($request->integer('page_size', 25), 100));

        return ValuationResource::collection(Valuation::query()->forTeam($teamId)->latest()->paginate($size))->response();
    }

    public function store(Request $request, CreateValuation $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['subject' => ['required', 'string', 'max:255'], 'property_id' => ['nullable', 'integer'], 'party_id' => ['nullable', 'integer'], 'valued_amount' => ['nullable', 'numeric', 'min:0'], 'fee_amount' => ['nullable', 'numeric', 'min:0'], 'currency' => ['nullable', 'string', 'size:3'], 'comparable_data' => ['sometimes', 'array'], 'recommendation' => ['sometimes', 'array'], 'scheduled_at' => ['nullable', 'date'], 'follow_up_at' => ['nullable', 'date']]);

        return (new ValuationResource($create->handle($user->current_team_id, $user->getAuthIdentifier(), $data)))->response()->setStatusCode(201);
    }

    public function show(Request $request, Valuation $valuation): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $valuation->team_id, 404);

        return (new ValuationResource($valuation))->response();
    }

    public function update(Request $request, Valuation $valuation, UpdateValuation $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $valuation->team_id, 404);
        $data = $request->validate(['subject' => ['sometimes', 'string', 'max:255'], 'valued_amount' => ['nullable', 'numeric', 'min:0'], 'fee_amount' => ['nullable', 'numeric', 'min:0'], 'currency' => ['nullable', 'string', 'size:3'], 'comparable_data' => ['sometimes', 'array'], 'recommendation' => ['sometimes', 'array'], 'scheduled_at' => ['nullable', 'date'], 'follow_up_at' => ['nullable', 'date']]);

        return (new ValuationResource($update->handle($valuation, $teamId, $data)))->response();
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

        return (new ValuationResource($schedule->handle($valuation, $teamId, (string) $request->validate(['scheduled_at' => ['required', 'date', 'after:now']])['scheduled_at'])))->response();
    }

    public function complete(Request $request, Valuation $valuation, CompleteValuation $complete): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $valuation->team_id, 404);

        return (new ValuationResource($complete->handle($valuation, $teamId, $request->validate(['valued_amount' => ['required', 'numeric', 'min:0'], 'recommendation' => ['sometimes', 'array'], 'follow_up_at' => ['nullable', 'date', 'after:now']]))))->response();
    }

    public function convert(Request $request, Valuation $valuation, ConvertValuation $convert): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $valuation->team_id, 404);

        return (new ValuationResource($convert->handle($valuation, $teamId, $request->validate(['type' => ['required', 'string', 'max:80'], 'id' => ['nullable', 'integer'], 'metadata' => ['sometimes', 'array']]))))->response();
    }

    public function comparables(Request $request, Valuation $valuation, CalculateComparables $calculate): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $valuation->team_id, 404);

        return response()->json(['data' => $calculate->handle($valuation, $teamId, $request->validate(['comparables' => ['required', 'array', 'min:1'], 'comparables.*.amount' => ['required', 'numeric', 'min:0']])['comparables'])]);
    }

    public function calculateHome(Request $request, CalculateHomeValuation $calculate): JsonResponse
    {
        $data = $request->validate(['property_size' => ['required', 'numeric', 'gt:0'], 'bedrooms' => ['required', 'integer', 'min:0'], 'bathrooms' => ['required', 'integer', 'min:0'], 'year_built' => ['required', 'integer', 'min:1000'], 'property_type' => ['required', 'string', 'max:40'], 'condition' => ['required', 'string', 'max:40'], 'location' => ['required', 'string', 'max:40'], 'base_price' => ['sometimes', 'numeric', 'gt:0']]);

        return (new ValuationCalculationResource($calculate->handle((float) $data['property_size'], $data['bedrooms'], $data['bathrooms'], $data['year_built'], $data['property_type'], $data['condition'], $data['location'], (float) ($data['base_price'] ?? 3000))))->response();
    }
}
