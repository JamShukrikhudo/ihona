<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ValuationsFilament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\RealEstate\Valuations\Models\Valuation;
use Liberu\RealEstate\ValuationsFilament\Resources\ValuationResource\Pages\CreateValuation;
use Liberu\RealEstate\ValuationsFilament\Resources\ValuationResource\Pages\EditValuation;
use Liberu\RealEstate\ValuationsFilament\Resources\ValuationResource\Pages\ListValuations;

final class ValuationResource extends Resource
{
    protected static ?string $model = Valuation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static string|\UnitEnum|null $navigationGroup = 'Недвижимость';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('subject')->required()->maxLength(255), Select::make('status')->options(['draft' => 'Draft', 'scheduled' => 'Scheduled', 'completed' => 'Completed', 'converted' => 'Converted', 'cancelled' => 'Cancelled'])->required(), TextInput::make('valued_amount')->numeric()->minValue(0), TextInput::make('fee_amount')->numeric()->minValue(0), TextInput::make('currency')->length(3)->default('GBP'), Textarea::make('comparable_data')->helperText('JSON comparable evidence.')->columnSpanFull(), Textarea::make('recommendation')->helperText('JSON recommendation and follow-up notes.')->columnSpanFull(), TextInput::make('scheduled_at')->datetime(), TextInput::make('follow_up_at')->datetime()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('subject')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('valued_amount')->money('GBP'), TextColumn::make('created_at')->dateTime()->sortable()])->recordActions([EditAction::make(), DeleteAction::make()])->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = auth()->user()?->current_team_id;

        return parent::getEloquentQuery()->when($teamId === null, fn (Builder $query): Builder => $query->whereRaw('1 = 0'), fn (Builder $query): Builder => $query->forTeam($teamId));
    }

    public static function getPages(): array
    {
        return ['index' => ListValuations::route('/'), 'create' => CreateValuation::route('/create'), 'edit' => EditValuation::route('/{record}/edit')];
    }
}
