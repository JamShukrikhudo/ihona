<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocumentsFilament\Resources\MediaDocumentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\MediaAndDocuments\Application\CreateMediaDocument as CreateMediaDocumentAction;
use Liberu\RealEstate\MediaAndDocumentsFilament\Resources\MediaDocumentResource;

final class CreateMediaDocument extends CreateRecord
{
    protected static string $resource = MediaDocumentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreateMediaDocumentAction::class)->handle($user->current_team_id, $user->getAuthIdentifier(), $data);
    }
}
