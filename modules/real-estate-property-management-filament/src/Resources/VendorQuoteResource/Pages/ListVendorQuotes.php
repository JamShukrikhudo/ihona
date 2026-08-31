<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementFilament\Resources\VendorQuoteResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\PropertyManagementFilament\Resources\VendorQuoteResource;

final class ListVendorQuotes extends ListRecords
{
    protected static string $resource = VendorQuoteResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
