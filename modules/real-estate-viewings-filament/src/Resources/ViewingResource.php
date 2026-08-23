<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ViewingsFilament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\RealEstate\Viewings\Models\Viewing;
use Liberu\RealEstate\ViewingsFilament\Resources\ViewingResource\Pages\CreateViewing;
use Liberu\RealEstate\ViewingsFilament\Resources\ViewingResource\Pages\EditViewing;
use Liberu\RealEstate\ViewingsFilament\Resources\ViewingResource\Pages\ListViewings;

final class ViewingResource extends Resource
{
    protected static ?string $model = Viewing::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|\UnitEnum|null $navigationGroup = 'Real Estate';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('subject')->required()->maxLength(255), Select::make('status')->options(['requested' => 'Requested', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'no_show' => 'No show'])->required(), TextInput::make('starts_at')->datetime(), TextInput::make('ends_at')->datetime()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('subject')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('starts_at')->dateTime()->sortable(), TextColumn::make('created_at')->dateTime()])->recordActions([EditAction::make(), DeleteAction::make()])->defaultSort('starts_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = auth()->user()?->current_team_id;

        return parent::getEloquentQuery()->when($teamId === null, fn (Builder $query): Builder => $query->whereRaw('1 = 0'), fn (Builder $query): Builder => $query->forTeam($teamId));
    }

    public static function getPages(): array
    {
        return ['index' => ListViewings::route('/'), 'create' => CreateViewing::route('/create'), 'edit' => EditViewing::route('/{record}/edit')];
    }
}
