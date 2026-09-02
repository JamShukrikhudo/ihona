<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\RealEstate\Properties\Application\CreatePriceAlert;
use Liberu\RealEstate\Properties\Application\DeletePriceAlert;
use Liberu\RealEstate\Properties\Application\UpdatePriceAlert;
use Liberu\RealEstate\Properties\Models\PropertyPriceAlert;
use Liberu\RealEstate\PropertiesApi\Http\Resources\PropertyPriceAlertResource;

final class PropertyPriceAlertController
{
    public function index(Request $request, int|string $property): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);

        return PropertyPriceAlertResource::collection(PropertyPriceAlert::query()->forUser($user->current_team_id, $user->getAuthIdentifier())->where('property_id', $property)->latest()->paginate(25))->response();
    }

    public function store(Request $request, int|string $property, CreatePriceAlert $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['alert_percentage' => ['required', 'numeric', 'min:0.1', 'max:100'], 'alert_frequency' => ['required', 'in:daily,weekly,monthly']]);

        return (new PropertyPriceAlertResource($create->handle($user->current_team_id, $user->getAuthIdentifier(), $property, $data)))->response()->setStatusCode(201);
    }

    public function update(Request $request, int|string $alert, UpdatePriceAlert $update): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['alert_percentage' => ['sometimes', 'numeric', 'min:0.1', 'max:100'], 'alert_frequency' => ['sometimes', 'in:daily,weekly,monthly'], 'is_active' => ['sometimes', 'boolean']]);

        return (new PropertyPriceAlertResource($update->handle($user->current_team_id, $user->getAuthIdentifier(), $alert, $data)))->response();
    }

    public function destroy(Request $request, int|string $alert, DeletePriceAlert $delete): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $delete->handle($user->current_team_id, $user->getAuthIdentifier(), $alert);

        return response()->json(['deleted' => true]);
    }
}
