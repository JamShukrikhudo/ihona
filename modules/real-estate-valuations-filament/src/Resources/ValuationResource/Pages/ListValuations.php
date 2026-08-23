<?php

namespace Liberu\RealEstate\ValuationsFilament\Resources\ValuationResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\ValuationsFilament\Resources\ValuationResource;

final class ListValuations extends ListRecords
{
    protected static string $resource = ValuationResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
