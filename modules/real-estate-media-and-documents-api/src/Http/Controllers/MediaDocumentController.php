<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocumentsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\MediaAndDocuments\Application\CreateMediaDocument;
use Liberu\RealEstate\MediaAndDocuments\Application\DeleteMediaDocument;
use Liberu\RealEstate\MediaAndDocuments\Application\UpdateMediaDocument;
use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;

final class MediaDocumentController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $pageSize = max(1, min($request->integer('page_size', 25), 100));
        return response()->json(['data' => MediaDocument::query()->forTeam($teamId)->latest()->paginate($pageSize)]);
    }

    public function store(Request $request, CreateMediaDocument $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $validated = $request->validate([
            'kind' => ['required', 'string', 'in:photo,floorplan,video,certificate,brochure,document'],
            'path' => ['required', 'string', 'max:2048'],
            'property_id' => ['nullable', 'integer'],
            'title' => ['nullable', 'string', 'max:255'],
            'rights' => ['sometimes', 'array'],
            'metadata' => ['sometimes', 'array'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'retention_until' => ['nullable', 'date'],
        ]);
        return response()->json(['data' => $create->handle($user->current_team_id, $user->getAuthIdentifier(), $validated)], 201);
    }

    public function show(Request $request, MediaDocument $mediaDocument): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $mediaDocument->team_id, 404);
        return response()->json(['data' => $mediaDocument]);
    }

    public function update(Request $request, MediaDocument $mediaDocument, UpdateMediaDocument $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $mediaDocument->team_id, 404);
        $validated = $request->validate(['path' => ['sometimes', 'string', 'max:2048'], 'title' => ['nullable', 'string', 'max:255'], 'rights' => ['sometimes', 'array'], 'metadata' => ['sometimes', 'array'], 'sort_order' => ['sometimes', 'integer', 'min:0'], 'retention_until' => ['nullable', 'date']]);
        return response()->json(['data' => $update->handle($mediaDocument, $teamId, $validated)]);
    }

    public function destroy(Request $request, MediaDocument $mediaDocument, DeleteMediaDocument $delete): Response
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $mediaDocument->team_id, 404);
        $delete->handle($mediaDocument, $teamId);
        return response()->noContent();
    }
}
