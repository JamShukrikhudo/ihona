<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesFilament\Resources\PropertyTemplateResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Properties\Models\PropertyTemplate;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyTemplateResource;

final class CreatePropertyTemplate extends CreateRecord
{
    protected static string $resource = PropertyTemplateResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return PropertyTemplate::query()->create(['team_id' => $teamId, 'name' => $data['name'], 'content' => $data['content']]);
    }
}
