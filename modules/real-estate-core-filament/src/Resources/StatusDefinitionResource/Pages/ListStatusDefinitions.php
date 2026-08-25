<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreFilament\Resources\StatusDefinitionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\CoreFilament\Resources\StatusDefinitionResource;

final class ListStatusDefinitions extends ListRecords
{
    protected static string $resource = StatusDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
