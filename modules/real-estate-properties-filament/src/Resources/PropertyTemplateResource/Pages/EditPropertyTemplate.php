<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesFilament\Resources\PropertyTemplateResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyTemplateResource;

final class EditPropertyTemplate extends EditRecord
{
    protected static string $resource = PropertyTemplateResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless((string) auth()->user()?->current_team_id === (string) $record->team_id, 403);
        $record->update(['name' => $data['name'], 'content' => $data['content']]);

        return $record;
    }
}
