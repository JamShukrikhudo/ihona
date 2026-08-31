<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Core\Application\CreateTerritory;
use Liberu\RealEstate\Core\Application\DeleteTerritory;
use Liberu\RealEstate\Core\Application\UpdateTerritory;
use Liberu\RealEstate\Core\Models\Territory;
use Liberu\RealEstate\CoreApi\Http\Resources\TerritoryResource;

final class TerritoryController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return TerritoryResource::collection(Territory::query()->forTeam($teamId)->latest()->paginate(max(1, min($request->integer('page_size', 25), 100))))->response();
    }

    public function store(Request $request, CreateTerritory $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);

        return (new TerritoryResource($create->handle($user->current_team_id, $request->validate($this->rules()))))->response()->setStatusCode(201);
    }

    public function show(Request $request, Territory $territory): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $territory->team_id, 404);

        return (new TerritoryResource($territory))->response();
    }

    public function update(Request $request, Territory $territory, UpdateTerritory $update): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $territory->team_id, 404);

        return (new TerritoryResource($update->handle($territory->team_id, $territory->getKey(), $request->validate($this->rules(true)))))->response();
    }

    public function destroy(Request $request, Territory $territory, DeleteTerritory $delete): Response
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $territory->team_id, 404);
        $delete->handle($territory->team_id, $territory->getKey());

        return response()->noContent();
    }

    /** @return array<string, list<string>> */
    private function rules(bool $update = false): array
    {
        return [
            'name' => [$update ? 'sometimes' : 'required', 'string', 'max:255'],
            'code' => [$update ? 'sometimes' : 'required', 'string', 'regex:/^[A-Za-z0-9-]{2,20}$/'],
            'boundary' => ['sometimes', 'array'],
            'metadata' => ['sometimes', 'array'],
        ];
    }
}
