<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocuments\Application;

use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;

final class DeleteMediaDocument
{
    public function handle(MediaDocument $document, int|string $teamId): void
    {
        abort_unless((string) $document->team_id === (string) $teamId, 404);
        $document->delete();
    }
}
