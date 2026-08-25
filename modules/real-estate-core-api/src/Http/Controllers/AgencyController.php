<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Core\Application\CreateAgency;
use Liberu\RealEstate\Core\Application\DeleteAgency;
use Liberu\RealEstate\Core\Application\UpdateAgency;
use Liberu\RealEstate\Core\Models\Agency;
use Liberu\RealEstate\CoreApi\Http\Resources\AgencyResource;

final class AgencyController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return AgencyResource::collection(Agency::query()->forTeam($teamId)->latest()->paginate(max(1, min($request->integer('page_size', 25), 100))))->response();
    }

    public function store(Request $request, CreateAgency $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);

        return (new AgencyResource($create->handle($user->current_team_id, $request->validate($this->rules()))))->response()->setStatusCode(201);
    }

    public function show(Request $request, Agency $agency): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $agency->team_id, 404);

        return (new AgencyResource($agency))->response();
    }

    public function update(Request $request, Agency $agency, UpdateAgency $update): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $agency->team_id, 404);

        return (new AgencyResource($update->handle($agency->team_id, $agency->getKey(), $request->validate($this->rules(true)))))->response();
    }

    public function destroy(Request $request, Agency $agency, DeleteAgency $delete): Response
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $agency->team_id, 404);
        $delete->handle($agency->team_id, $agency->getKey());

        return response()->noContent();
    }

    /** @return array<string, list<string>> */
    private function rules(bool $update = false): array
    {
        return [
            'name' => [$update ? 'sometimes' : 'required', 'string', 'max:255'],
            'code' => [$update ? 'sometimes' : 'required', 'string', 'regex:/^[A-Za-z0-9-]{2,20}$/'],
            'active' => ['sometimes', 'boolean'],
            'metadata' => ['sometimes', 'array'],
        ];
    }
}
