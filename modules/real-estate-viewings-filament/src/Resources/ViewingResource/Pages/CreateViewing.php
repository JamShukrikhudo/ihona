<?php
namespace Liberu\RealEstate\ViewingsFilament\Resources\ViewingResource\Pages;
use Filament\Resources\Pages\CreateRecord; use Liberu\RealEstate\ViewingsFilament\Resources\ViewingResource;
final class CreateViewing extends CreateRecord { protected static string $resource=ViewingResource::class; protected function mutateFormDataBeforeCreate(array $data):array{$data['team_id']=auth()->user()->current_team_id;$data['created_by']=auth()->id();return $data;} }
