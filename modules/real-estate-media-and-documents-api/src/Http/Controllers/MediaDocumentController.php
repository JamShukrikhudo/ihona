<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocumentsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\MediaAndDocuments\Application\CreateMediaDocument;
use Liberu\RealEstate\MediaAndDocuments\Application\DeleteMediaDocument;
use Liberu\RealEstate\MediaAndDocuments\Application\GeneratePropertyBrochure;
use Liberu\RealEstate\MediaAndDocuments\Application\ReorderMediaDocument;
use Liberu\RealEstate\MediaAndDocuments\Application\SetMediaDocumentRetention;
use Liberu\RealEstate\MediaAndDocuments\Application\UpdateMediaDocument;
use Liberu\RealEstate\MediaAndDocuments\Application\UpdateMediaRights;
use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;
use Liberu\RealEstate\MediaAndDocumentsApi\Http\Resources\BrochureResource;
use Liberu\RealEstate\MediaAndDocumentsApi\Http\Resources\MediaDocumentResource;

final class MediaDocumentController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $pageSize = max(1, min($request->integer('page_size', 25), 100));

        return MediaDocumentResource::collection(MediaDocument::query()->forTeam($teamId)->latest()->paginate($pageSize))->response();
    }

    public function brochure(Request $request, GeneratePropertyBrochure $generate): JsonResponse
    {
        $data = $request->validate(['property' => ['required', 'array'], 'property.id' => ['required'], 'property.title' => ['required', 'string'], 'property.price' => ['required', 'numeric'], 'options' => ['sometimes', 'array']]);

        return (new BrochureResource($generate->handle($data['property'], $data['options'] ?? [])))->response();
    }

    public function store(Request $request, CreateMediaDocument $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $validated = $request->validate([
            'kind' => ['required', 'string', 'in:photo,floorplan,siteplan,video,certificate,brochure,document'],
            'path' => ['required', 'string', 'max:2048'],
            'property_id' => ['nullable', 'integer'],
            'title' => ['nullable', 'string', 'max:255'],
            'rights' => ['sometimes', 'array'],
            'metadata' => ['sometimes', 'array'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'retention_until' => ['nullable', 'date'],
        ]);

        return (new MediaDocumentResource($create->handle($user->current_team_id, $user->getAuthIdentifier(), $validated)))->response()->setStatusCode(201);
    }

    public function show(Request $request, MediaDocument $mediaDocument): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $mediaDocument->team_id, 404);

        return (new MediaDocumentResource($mediaDocument))->response();
    }

    public function update(Request $request, MediaDocument $mediaDocument, UpdateMediaDocument $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $mediaDocument->team_id, 404);
        $validated = $request->validate(['path' => ['sometimes', 'string', 'max:2048'], 'title' => ['nullable', 'string', 'max:255'], 'rights' => ['sometimes', 'array'], 'metadata' => ['sometimes', 'array'], 'sort_order' => ['sometimes', 'integer', 'min:0'], 'retention_until' => ['nullable', 'date']]);

        return (new MediaDocumentResource($update->handle($mediaDocument, $teamId, $validated)))->response();
    }

    public function rights(Request $request, MediaDocument $mediaDocument, UpdateMediaRights $update): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $mediaDocument->team_id, 404);

        return (new MediaDocumentResource($update->handle($mediaDocument, $teamId, $request->validate(['rights' => ['required', 'array']])['rights'])))->response();
    }

    public function reorder(Request $request, MediaDocument $mediaDocument, ReorderMediaDocument $reorder): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $mediaDocument->team_id, 404);

        return (new MediaDocumentResource($reorder->handle($mediaDocument, $teamId, $request->validate(['sort_order' => ['required', 'integer', 'min:0']])['sort_order'])))->response();
    }

    public function retention(Request $request, MediaDocument $mediaDocument, SetMediaDocumentRetention $setRetention): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $mediaDocument->team_id, 404);
        $value = $request->validate(['retention_until' => ['nullable', 'date']])['retention_until'] ?? null;

        return (new MediaDocumentResource($setRetention->handle($mediaDocument, $teamId, $value)))->response();
    }

    public function destroy(Request $request, MediaDocument $mediaDocument, DeleteMediaDocument $delete): Response
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless((string) $teamId === (string) $mediaDocument->team_id, 404);
        $delete->handle($mediaDocument, $teamId);

        return response()->noContent();
    }
}
