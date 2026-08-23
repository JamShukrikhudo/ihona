<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocuments\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;

final class UpdateMediaDocument
{
    /** @param array<string, mixed> $attributes */
    public function handle(MediaDocument $document, int|string $teamId, array $attributes): MediaDocument
    {
        abort_unless((string) $document->team_id === (string) $teamId, 404);
        if (array_key_exists('path', $attributes) && trim((string) $attributes['path']) === '') {
            throw ValidationException::withMessages(['path' => 'A storage path is required.']);
        }
        $document->fill($attributes);
        $document->save();

        return $document->fresh();
    }
}
