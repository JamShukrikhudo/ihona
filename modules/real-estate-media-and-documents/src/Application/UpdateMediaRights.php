<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocuments\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;

final class UpdateMediaRights
{
    /** @param array<string, mixed> $rights */
    public function handle(MediaDocument $document, int|string $teamId, array $rights): MediaDocument
    {
        if ((string) $document->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['document' => 'The document does not belong to this team.']);
        }
        $document->forceFill(['rights' => $rights])->save();

        return $document->refresh();
    }
}
