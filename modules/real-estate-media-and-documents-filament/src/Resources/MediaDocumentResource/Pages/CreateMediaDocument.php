<?php
namespace Liberu\RealEstate\MediaAndDocumentsFilament\Resources\MediaDocumentResource\Pages;
use Filament\Resources\Pages\CreateRecord;
use Liberu\RealEstate\MediaAndDocumentsFilament\Resources\MediaDocumentResource;
final class CreateMediaDocument extends CreateRecord { protected static string $resource = MediaDocumentResource::class; protected function mutateFormDataBeforeCreate(array $data): array { $data['team_id']=auth()->user()->current_team_id; $data['created_by']=auth()->id(); return $data; } }
