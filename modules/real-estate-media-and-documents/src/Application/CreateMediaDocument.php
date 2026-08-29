<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocuments\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;

final class CreateMediaDocument
{
    /** @param array<string, mixed> $attributes */
    public function handle(int|string $teamId, int|string $actorId, array $attributes): MediaDocument
    {
        $kind = strtolower(trim((string) ($attributes['kind'] ?? '')));
        $path = trim((string) ($attributes['path'] ?? ''));
        if (! in_array($kind, ['photo', 'floorplan', 'siteplan', 'video', 'certificate', 'brochure', 'document'], true)) {
            throw ValidationException::withMessages(['kind' => 'A supported media or document kind is required.']);
        }
        if ($path === '') {
            throw ValidationException::withMessages(['path' => 'A storage path is required.']);
        }

        return DB::transaction(fn (): MediaDocument => MediaDocument::query()->create([
            'team_id' => $teamId,
            'created_by' => $actorId,
            'property_id' => $attributes['property_id'] ?? null,
            'kind' => $kind,
            'path' => $path,
            'title' => trim((string) ($attributes['title'] ?? '')) ?: null,
            'rights' => $attributes['rights'] ?? [],
            'metadata' => $attributes['metadata'] ?? [],
            'sort_order' => (int) ($attributes['sort_order'] ?? 0),
            'retention_until' => $attributes['retention_until'] ?? null,
        ]));
    }
}
