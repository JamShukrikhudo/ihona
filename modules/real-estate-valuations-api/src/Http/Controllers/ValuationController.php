<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ValuationsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\Valuations\Application\CalculateComparables;
use Liberu\RealEstate\Valuations\Application\CalculateHomeValuation;
use Liberu\RealEstate\Valuations\Application\CalculateMortgage;
use Liberu\RealEstate\Valuations\Application\CalculateRentalYield;
use Liberu\RealEstate\Valuations\Application\CompleteValuation;
use Liberu\RealEstate\Valuations\Application\ConvertValuation;
use Liberu\RealEstate\Valuations\Application\CreateValuation;
use Liberu\RealEstate\Valuations\Application\DeleteValuation;
use Liberu\RealEstate\Valuations\Application\GenerateNeuralPropertyValuation;
use Liberu\RealEstate\Valuations\Application\GeneratePropertyValuation;
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

    public function calculateNeuralProperty(Request $request, GenerateNeuralPropertyValuation $generate): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $data = $request->validate(['property_id' => ['required', 'integer'], 'comparables_count' => ['sometimes', 'integer', 'min:0'], 'training_samples' => ['sometimes', 'integer', 'min:0']]);
        $property = Property::query()->forTeam($teamId)->findOrFail($data['property_id']);

        return response()->json(['data' => $generate->handle($property, $data['comparables_count'] ?? 0, $data['training_samples'] ?? 0)]);
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

    public function calculateProperty(Request $request, GeneratePropertyValuation $calculate): JsonResponse
    {
        $data = $request->validate([
            'property' => ['required', 'array'],
            'property.area_sqft' => ['required', 'numeric', 'gt:0'],
            'property.bedrooms' => ['required', 'integer', 'min:0'],
            'property.bathrooms' => ['required', 'integer', 'min:0'],
            'property.year_built' => ['required', 'integer', 'min:1000'],
            'property.property_type' => ['required', 'string', 'max:40'],
            'property.address' => ['sometimes', 'string', 'max:500'],
            'property.location' => ['sometimes', 'string', 'max:255'],
            'property.status' => ['sometimes', 'string', 'max:40'],
            'property.price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'property.latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'property.longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'property.is_featured' => ['sometimes', 'boolean'],
            'property.list_date' => ['sometimes', 'nullable', 'date'],
            'comparables_count' => ['sometimes', 'integer', 'min:0', 'max:1000'],
            'training_samples' => ['sometimes', 'integer', 'min:0', 'max:1000000'],
        ]);

        return (new ValuationCalculationResource($calculate->handle(
            $data['property'],
            (int) ($data['comparables_count'] ?? 0),
            (int) ($data['training_samples'] ?? 0),
        )))->response();
    }

    public function calculateMortgage(Request $request, CalculateMortgage $calculate): JsonResponse
    {
        $data = $request->validate([
            'property_price' => ['required', 'numeric', 'gt:0'],
            'loan_amount' => ['required', 'numeric', 'gt:0', 'lte:property_price'],
            'interest_rate' => ['required', 'numeric', 'between:0,100'],
            'loan_term_years' => ['required', 'integer', 'between:1,50'],
        ]);

        return (new ValuationCalculationResource($calculate->handle(
            (float) $data['property_price'],
            (float) $data['loan_amount'],
            (float) $data['interest_rate'],
            (int) $data['loan_term_years'],
        )))->response();
    }

    public function calculateRentalYield(Request $request, CalculateRentalYield $calculate): JsonResponse
    {
        $data = $request->validate([
            'property_value' => ['required', 'numeric', 'gt:0'],
            'annual_rental_income' => ['required', 'numeric', 'min:0'],
            'annual_expenses' => ['sometimes', 'numeric', 'min:0'],
        ]);

        return (new ValuationCalculationResource($calculate->handle(
            (float) $data['property_value'],
            (float) $data['annual_rental_income'],
            (float) ($data['annual_expenses'] ?? 0),
        )))->response();
    }
}
