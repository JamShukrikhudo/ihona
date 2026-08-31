<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesFilament\Resources\PropertyCategoryResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyCategoryResource;

final class EditPropertyCategory extends EditRecord
{
    protected static string $resource = PropertyCategoryResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless((string) auth()->user()?->current_team_id === (string) $record->team_id, 403);
        $record->update([
            'name' => $data['name'],
            'slug' => filled($data['slug'] ?? null) ? $data['slug'] : str($data['name'])->slug()->toString(),
        ]);

        return $record;
    }
}
