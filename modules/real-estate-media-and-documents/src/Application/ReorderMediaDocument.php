<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocuments\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;

final class ReorderMediaDocument
{
    public function handle(MediaDocument $document, int|string $teamId, int $sortOrder): MediaDocument
    {
        if ((string) $document->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['document' => 'The document does not belong to this team.']);
        }
        if ($sortOrder < 0) {
            throw ValidationException::withMessages(['sort_order' => 'The sort order cannot be negative.']);
        }
        $document->forceFill(['sort_order' => $sortOrder])->save();

        return $document->refresh();
    }
}
