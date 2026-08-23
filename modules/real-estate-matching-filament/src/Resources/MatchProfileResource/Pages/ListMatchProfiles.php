<?php
namespace Liberu\RealEstate\MatchingFilament\Resources\MatchProfileResource\Pages;
use Filament\Resources\Pages\ListRecords; use Liberu\RealEstate\MatchingFilament\Resources\MatchProfileResource;
final class ListMatchProfiles extends ListRecords { protected static string $resource=MatchProfileResource::class; protected function getHeaderActions():array{return [\Filament\Actions\CreateAction::make()];} }
