<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\RealEstate\PropertyManagement\Models\MaintenanceRequest;
use Liberu\RealEstate\PropertyManagementFilament\Resources\MaintenanceRequestResource\Pages\CreateMaintenanceRequest;
use Liberu\RealEstate\PropertyManagementFilament\Resources\MaintenanceRequestResource\Pages\EditMaintenanceRequest;
use Liberu\RealEstate\PropertyManagementFilament\Resources\MaintenanceRequestResource\Pages\ListMaintenanceRequests;

final class MaintenanceRequestResource extends Resource
{
    protected static ?string $model = MaintenanceRequest::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('property_id')->required()->numeric(), TextInput::make('title')->required()->maxLength(255), Textarea::make('description')->required()->columnSpanFull(), Select::make('priority')->options(['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'])->required(), Select::make('status')->options(['pending' => 'Pending', 'in_progress' => 'In progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'])->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->searchable(), TextColumn::make('priority')->badge(), TextColumn::make('status')->badge(), TextColumn::make('requested_date')->date()->sortable()])->defaultSort('requested_date', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $team = auth()->user()?->current_team_id;

        return parent::getEloquentQuery()->when($team === null, fn (Builder $q) => $q->whereRaw('1=0'), fn (Builder $q) => $q->forTeam($team));
    }

    public static function getPages(): array
    {
        return ['index' => ListMaintenanceRequests::route('/'), 'create' => CreateMaintenanceRequest::route('/create'), 'edit' => EditMaintenanceRequest::route('/{record}/edit')];
    }
}
