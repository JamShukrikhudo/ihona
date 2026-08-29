<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesFilament\Resources\PropertyCategoryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Properties\Models\PropertyCategory;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyCategoryResource;

final class CreatePropertyCategory extends CreateRecord
{
    protected static string $resource = PropertyCategoryResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return PropertyCategory::query()->create([
            'team_id' => $teamId,
            'name' => $data['name'],
            'slug' => filled($data['slug'] ?? null) ? $data['slug'] : str($data['name'])->slug()->toString(),
        ]);
    }
}
