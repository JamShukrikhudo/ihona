<?php
namespace Liberu\RealEstate\ListingsFilament\Resources\ListingResource\Pages;
use Filament\Resources\Pages\ListRecords; use Liberu\RealEstate\ListingsFilament\Resources\ListingResource;
final class ListListings extends ListRecords { protected static string $resource=ListingResource::class; protected function getHeaderActions():array{return [\Filament\Actions\CreateAction::make()];} }
