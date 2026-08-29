<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Properties\Models\PropertyCategory;
use Liberu\RealEstate\PropertiesApi\Http\Resources\PropertyCategoryResource;

final class PropertyCategoryController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return PropertyCategoryResource::collection(PropertyCategory::query()->forTeam($teamId)->orderBy('name')->get())->response();
    }

    public function store(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'slug' => ['sometimes', 'nullable', 'string', 'max:140']]);
        $data['slug'] = str($data['slug'] ?? $data['name'])->slug()->limit(140, '')->toString();
        abort_if(PropertyCategory::query()->forTeam($teamId)->where('slug', $data['slug'])->exists(), 422, 'The category slug is already in use.');

        return (new PropertyCategoryResource(PropertyCategory::query()->create(['team_id' => $teamId, ...$data])))->response()->setStatusCode(201);
    }

    public function update(Request $request, PropertyCategory $category): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null && (string) $category->team_id === (string) $teamId, 404);
        $data = $request->validate(['name' => ['sometimes', 'string', 'max:120'], 'slug' => ['sometimes', 'string', 'max:140']]);
        if (array_key_exists('name', $data) && ! array_key_exists('slug', $data)) {
            $data['slug'] = str($data['name'])->slug()->limit(140, '')->toString();
        }
        if (isset($data['slug']) && PropertyCategory::query()->forTeam($teamId)->where('slug', $data['slug'])->where('id', '!=', $category->getKey())->exists()) {
            abort(422, 'The category slug is already in use.');
        }
        $category->update($data);

        return (new PropertyCategoryResource($category->fresh()))->response();
    }

    public function destroy(Request $request, PropertyCategory $category): Response
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $category->team_id, 404);
        $category->delete();

        return response()->noContent();
    }
}
