<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesFilament\Resources\PropertySavedSearchResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\Properties\Models\PropertySavedSearch;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertySavedSearchResource;

final class CreatePropertySavedSearch extends CreateRecord
{
    protected static string $resource = PropertySavedSearchResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return PropertySavedSearch::query()->create(['team_id' => $user->current_team_id, 'user_id' => $user->getAuthIdentifier(), 'name' => $data['name'], 'criteria' => $data['criteria']]);
    }
}
