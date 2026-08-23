<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ViewingsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Viewings\Application\CreateViewing;
use Liberu\RealEstate\Viewings\Application\DeleteViewing;
use Liberu\RealEstate\Viewings\Application\UpdateViewing;
use Liberu\RealEstate\Viewings\Models\Viewing;

final class ViewingController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $size = max(1, min($request->integer('page_size', 25), 100));

        return response()->json(['data' => Viewing::query()->forTeam($teamId)->latest('starts_at')->paginate($size)]);
    }

    public function store(Request $request, CreateViewing $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate(['subject' => ['required', 'string', 'max:255'], 'property_id' => ['nullable', 'integer'], 'party_id' => ['nullable', 'integer'], 'starts_at' => ['required', 'date'], 'ends_at' => ['nullable', 'date', 'after:starts_at'], 'access' => ['sometimes', 'array'], 'accompaniment' => ['sometimes', 'array'], 'reminders' => ['sometimes', 'array']]);

        return response()->json(['data' => $create->handle($user->current_team_id, $user->getAuthIdentifier(), $data)], 201);
    }

    public function show(Request $request, Viewing $viewing): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $viewing->team_id, 404);

        return response()->json(['data' => $viewing]);
    }

    public function update(Request $request, Viewing $viewing, UpdateViewing $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $viewing->team_id, 404);
        $data = $request->validate(['subject' => ['sometimes', 'string', 'max:255'], 'starts_at' => ['sometimes', 'date'], 'ends_at' => ['nullable', 'date'], 'status' => ['sometimes', 'string', 'in:requested,confirmed,completed,cancelled,no_show'], 'access' => ['sometimes', 'array'], 'accompaniment' => ['sometimes', 'array'], 'reminders' => ['sometimes', 'array'], 'feedback' => ['sometimes', 'array'], 'no_show' => ['sometimes', 'boolean']]);

        return response()->json(['data' => $update->handle($viewing, $teamId, $data)]);
    }

    public function destroy(Request $request, Viewing $viewing, DeleteViewing $delete): Response
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $viewing->team_id, 404);
        $delete->handle($viewing, $teamId);

        return response()->noContent();
    }
}
