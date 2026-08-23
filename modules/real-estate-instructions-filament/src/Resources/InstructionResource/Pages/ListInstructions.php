<?php

namespace Liberu\RealEstate\InstructionsFilament\Resources\InstructionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\InstructionsFilament\Resources\InstructionResource;

final class ListInstructions extends ListRecords
{
    protected static string $resource = InstructionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
