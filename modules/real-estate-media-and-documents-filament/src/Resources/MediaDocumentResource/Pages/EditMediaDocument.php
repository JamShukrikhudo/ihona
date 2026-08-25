<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocumentsFilament\Resources\MediaDocumentResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\MediaAndDocuments\Application\UpdateMediaDocument as UpdateMediaDocumentAction;
use Liberu\RealEstate\MediaAndDocumentsFilament\Resources\MediaDocumentResource;

final class EditMediaDocument extends EditRecord
{
    protected static string $resource = MediaDocumentResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null && (string) $user->current_team_id === (string) $record->team_id, 403);

        return app(UpdateMediaDocumentAction::class)->handle($record, $user->current_team_id, $data);
    }
}
