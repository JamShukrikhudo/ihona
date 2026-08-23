<?php
namespace Liberu\RealEstate\ViewingsFilament\Resources\ViewingResource\Pages;
use Filament\Resources\Pages\ListRecords; use Liberu\RealEstate\ViewingsFilament\Resources\ViewingResource;
final class ListViewings extends ListRecords { protected static string $resource=ViewingResource::class; protected function getHeaderActions():array{return [\Filament\Actions\CreateAction::make()];} }
