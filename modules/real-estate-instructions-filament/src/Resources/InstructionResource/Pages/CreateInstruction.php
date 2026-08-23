<?php
namespace Liberu\RealEstate\InstructionsFilament\Resources\InstructionResource\Pages;
use Filament\Resources\Pages\CreateRecord; use Liberu\RealEstate\InstructionsFilament\Resources\InstructionResource;
final class CreateInstruction extends CreateRecord { protected static string $resource=InstructionResource::class; protected function mutateFormDataBeforeCreate(array $data):array{$data['team_id']=auth()->user()->current_team_id;$data['created_by']=auth()->id();return $data;} }
