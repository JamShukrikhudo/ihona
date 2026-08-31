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
use Liberu\RealEstate\PropertyManagement\Models\WorkOrder;
use Liberu\RealEstate\PropertyManagementFilament\Resources\WorkOrderResource\Pages\CreateWorkOrder;
use Liberu\RealEstate\PropertyManagementFilament\Resources\WorkOrderResource\Pages\EditWorkOrder;
use Liberu\RealEstate\PropertyManagementFilament\Resources\WorkOrderResource\Pages\ListWorkOrders;

final class WorkOrderResource extends Resource
{
    protected static ?string $model = WorkOrder::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('property_id')->required()->numeric(), TextInput::make('vendor_id')->numeric(), TextInput::make('title')->required()->maxLength(255), Textarea::make('description')->required()->columnSpanFull(), TextInput::make('work_type')->required()->maxLength(100), Select::make('status')->options(['pending' => 'Pending', 'approved' => 'Approved', 'scheduled' => 'Scheduled', 'in_progress' => 'In progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'])->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->searchable(), TextColumn::make('work_type'), TextColumn::make('status')->badge(), TextColumn::make('scheduled_date')->dateTime()->sortable()])->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $team = auth()->user()?->current_team_id;
        return parent::getEloquentQuery()->when($team === null, fn (Builder $q) => $q->whereRaw('1=0'), fn (Builder $q) => $q->forTeam($team));
    }

    public static function getPages(): array
    {
        return ['index' => ListWorkOrders::route('/'), 'create' => CreateWorkOrder::route('/create'), 'edit' => EditWorkOrder::route('/{record}/edit')];
    }
}
