<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocumentsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;
use Liberu\RealEstate\MediaAndDocumentsApi\Http\Resources\MediaDocumentResource;

final class VirtualStagingController
{
    public function styles(): JsonResponse
    {
        return response()->json(['data' => ['styles' => ['modern' => 'Modern', 'luxury' => 'Luxury', 'scandinavian' => 'Scandinavian', 'industrial' => 'Industrial']]]);
    }

    public function upload(Request $r): JsonResponse
    {
        $u = $r->user();
        abort_unless($u?->current_team_id !== null, 403);
        $d = $r->validate(['property_id' => 'required|integer', 'image' => 'required|image', 'staging_style' => 'required|string|max:40', 'auto_stage' => 'boolean']);
        $path = $d['image']->store('property-images/'.$u->current_team_id, 'public');
        $image = MediaDocument::create(['team_id' => $u->current_team_id, 'created_by' => $u->getAuthIdentifier(), 'property_id' => $d['property_id'], 'kind' => 'photo', 'path' => $path, 'metadata' => ['staged' => false]]);
        $staged = $this->stageRecord($image, $d['staging_style'], $u->current_team_id);

        return response()->json(['data' => ['image' => new MediaDocumentResource($image), 'staged_image' => new MediaDocumentResource($staged), 'has_staged_versions' => true]], 201);
    }

    public function stage(Request $r, MediaDocument $mediaDocument): JsonResponse
    {
        $u = $r->user();
        abort_unless((string) $u?->current_team_id === (string) $mediaDocument->team_id, 404);
        if (data_get($mediaDocument->metadata, 'staged', false)) {
            throw ValidationException::withMessages(['media' => 'This media is already staged.']);
        }$style = $r->validate(['staging_style' => 'required|string|max:40'])['staging_style'];
        $staged = $this->stageRecord($mediaDocument, $style, $u->current_team_id);

        return response()->json(['data' => ['image' => new MediaDocumentResource($mediaDocument), 'staged_image' => new MediaDocumentResource($staged), 'has_staged_versions' => true]], 201);
    }

    private function stageRecord(MediaDocument $image, string $style, int|string $teamId): MediaDocument
    {
        return MediaDocument::create(['team_id' => $teamId, 'created_by' => $image->created_by, 'property_id' => $image->property_id, 'kind' => 'photo', 'path' => $image->path, 'metadata' => ['staged' => true, 'source_media_id' => $image->getKey(), 'staging_style' => $style]]);
    }
}
