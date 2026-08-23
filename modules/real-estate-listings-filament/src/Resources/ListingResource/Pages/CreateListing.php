<?php
namespace Liberu\RealEstate\ListingsFilament\Resources\ListingResource\Pages;
use Filament\Resources\Pages\CreateRecord; use Liberu\RealEstate\ListingsFilament\Resources\ListingResource;
final class CreateListing extends CreateRecord { protected static string $resource=ListingResource::class; protected function mutateFormDataBeforeCreate(array $data):array{$data['team_id']=auth()->user()->current_team_id;$data['created_by']=auth()->id();return $data;} }
