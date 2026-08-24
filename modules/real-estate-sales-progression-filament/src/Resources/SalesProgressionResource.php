<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgressionFilament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\SalesProgression\Application\DeleteSalesProgression;
use Liberu\RealEstate\SalesProgression\Models\SalesProgression;
use Liberu\RealEstate\SalesProgressionFilament\Resources\SalesProgressionResource\Pages\CreateSalesProgression;
use Liberu\RealEstate\SalesProgressionFilament\Resources\SalesProgressionResource\Pages\EditSalesProgression;
use Liberu\RealEstate\SalesProgressionFilament\Resources\SalesProgressionResource\Pages\ListSalesProgressions;

final class SalesProgressionResource extends Resource
{
    protected static ?string $model = SalesProgression::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('subject')->required()->maxLength(255), TextInput::make('property_id')->numeric(), TextInput::make('offer_id')->numeric(), TextInput::make('status')->required(), Textarea::make('notes')->columnSpanFull()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('subject')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('created_at')->dateTime()])->recordActions([
            EditAction::make(),
            DeleteAction::make()->action(function (Model $record): void {
                $teamId = auth()->user()?->current_team_id;
                abort_unless($teamId !== null, 403);
                app(DeleteSalesProgression::class)->handle($record, $teamId);
            }),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = auth()->user()?->current_team_id;

        return parent::getEloquentQuery()->when($teamId === null, fn (Builder $query): Builder => $query->whereRaw('1 = 0'), fn (Builder $query): Builder => $query->forTeam($teamId));
    }

    public static function getPages(): array
    {
        return ['index' => ListSalesProgressions::route('/'), 'create' => CreateSalesProgression::route('/create'), 'edit' => EditSalesProgression::route('/{record}/edit')];
    }
}
