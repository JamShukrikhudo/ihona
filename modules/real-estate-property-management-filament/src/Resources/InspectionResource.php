<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementFilament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\RealEstate\PropertyManagement\Domain\InspectionType;
use Liberu\RealEstate\PropertyManagement\Models\Inspection;
use Liberu\RealEstate\PropertyManagementFilament\Resources\InspectionResource\Pages\CreateInspection;
use Liberu\RealEstate\PropertyManagementFilament\Resources\InspectionResource\Pages\EditInspection;
use Liberu\RealEstate\PropertyManagementFilament\Resources\InspectionResource\Pages\ListInspections;

final class InspectionResource extends Resource
{
    protected static ?string $model = Inspection::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('property_id')->required()->numeric(),
            Select::make('type')->options(collect(InspectionType::cases())->mapWithKeys(fn ($case) => [$case->value => str($case->value)->replace('_', ' ')->title()])->all())->required(),
            Select::make('status')->options(['scheduled' => 'Scheduled', 'in_progress' => 'In progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'])->required(),
            DateTimePicker::make('scheduled_at')->required(),
            Textarea::make('notes')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('property_id')->sortable(), TextColumn::make('type')->badge(), TextColumn::make('status')->badge(), TextColumn::make('scheduled_at')->dateTime()->sortable()])->defaultSort('scheduled_at');
    }

    public static function getEloquentQuery(): Builder
    {
        $team = auth()->user()?->current_team_id;

        return parent::getEloquentQuery()->when($team === null, fn (Builder $q) => $q->whereRaw('1=0'), fn (Builder $q) => $q->forTeam($team));
    }

    public static function getPages(): array
    {
        return ['index' => ListInspections::route('/'), 'create' => CreateInspection::route('/create'), 'edit' => EditInspection::route('/{record}/edit')];
    }
}
