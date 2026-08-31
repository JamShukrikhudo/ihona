<?php

declare(strict_types=1);

namespace Liberu\RealEstate\LettingsFilament\Resources\RentalApplicationResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\LettingsFilament\Resources\RentalApplicationResource;

final class ListRentalApplications extends ListRecords
{
    protected static string $resource = RentalApplicationResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
