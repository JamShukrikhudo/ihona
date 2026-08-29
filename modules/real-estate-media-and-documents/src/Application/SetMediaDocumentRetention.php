<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocuments\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;

final class SetMediaDocumentRetention
{
    public function handle(MediaDocument $document, int|string $teamId, ?string $retentionUntil): MediaDocument
    {
        if ((string) $document->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['document' => 'The document does not belong to this team.']);
        }
        if ($retentionUntil !== null && strtotime($retentionUntil) === false) {
            throw ValidationException::withMessages(['retention_until' => 'The retention date must be valid.']);
        }
        $document->forceFill(['retention_until' => $retentionUntil])->save();

        return $document->refresh();
    }
}
