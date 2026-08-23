<?php

namespace Liberu\RealEstate\MatchingFilament\Resources\MatchProfileResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\MatchingFilament\Resources\MatchProfileResource;

final class ListMatchProfiles extends ListRecords
{
    protected static string $resource = MatchProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
