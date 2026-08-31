<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Properties\Models\PropertyTemplate;
use Liberu\RealEstate\PropertiesApi\Http\Resources\PropertyTemplateResource;

final class PropertyTemplateController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return PropertyTemplateResource::collection(PropertyTemplate::query()->forTeam($teamId)->orderBy('name')->get())->response();
    }

    public function store(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'content' => ['required', 'string', 'max:100000']]);

        return (new PropertyTemplateResource(PropertyTemplate::query()->create(['team_id' => $teamId, ...$data])))->response()->setStatusCode(201);
    }

    public function update(Request $request, PropertyTemplate $template): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $template->team_id, 404);
        $template->update($request->validate(['name' => ['sometimes', 'string', 'max:120'], 'content' => ['sometimes', 'string', 'max:100000']]));

        return (new PropertyTemplateResource($template->fresh()))->response();
    }

    public function destroy(Request $request, PropertyTemplate $template): Response
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $template->team_id, 404);
        $template->delete();

        return response()->noContent();
    }
}
