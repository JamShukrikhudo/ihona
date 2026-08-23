<?php

namespace Liberu\RealEstate\MediaAndDocumentsFilament\Resources\MediaDocumentResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\MediaAndDocumentsFilament\Resources\MediaDocumentResource;

final class ListMediaDocuments extends ListRecords
{
    protected static string $resource = MediaDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
