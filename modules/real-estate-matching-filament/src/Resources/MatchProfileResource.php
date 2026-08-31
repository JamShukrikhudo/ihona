<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MatchingFilament\Resources;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\RealEstate\Matching\Application\CalculateMatchScore;
use Liberu\RealEstate\Matching\Models\MatchProfile;
use Liberu\RealEstate\MatchingFilament\Resources\MatchProfileResource\Pages\CreateMatchProfile;
use Liberu\RealEstate\MatchingFilament\Resources\MatchProfileResource\Pages\EditMatchProfile;
use Liberu\RealEstate\MatchingFilament\Resources\MatchProfileResource\Pages\ListMatchProfiles;

final class MatchProfileResource extends Resource
{
    protected static ?string $model = MatchProfile::class;

    protected static ?string $modelLabel = 'Профиль подбора';

    protected static ?string $pluralModelLabel = 'Профили подбора';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static string|\UnitEnum|null $navigationGroup = 'Недвижимость';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('subject')->required()->maxLength(255), TextInput::make('score')->numeric()->minValue(0)->maxValue(100), TextInput::make('party_id')->numeric(), Textarea::make('requirements')->json(), Textarea::make('affordability')->json(), Textarea::make('preferences')->json(), Textarea::make('alerts')->json(), Textarea::make('feedback')->json(), Textarea::make('exclusions')->json()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('subject')->searchable(), TextColumn::make('score')->sortable(), TextColumn::make('created_at')->dateTime()->sortable()])->recordActions([
            EditAction::make(),
            Action::make('calculate_score')->form([Textarea::make('criteria')->json()->required(), Textarea::make('property')->json()->required()])->action(fn (MatchProfile $record, array $data): MatchProfile => tap($record, function (MatchProfile $record) use ($data): void {
                $score = app(CalculateMatchScore::class)->handle($data['criteria'], $data['property']);
                $record->forceFill(['score' => (int) round($score['match_score'])])->save();
            })),
            DeleteAction::make(),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = auth()->user()?->current_team_id;

        return parent::getEloquentQuery()->when($teamId === null, fn (Builder $query): Builder => $query->whereRaw('1 = 0'), fn (Builder $query): Builder => $query->forTeam($teamId));
    }

    public static function getPages(): array
    {
        return ['index' => ListMatchProfiles::route('/'), 'create' => CreateMatchProfile::route('/create'), 'edit' => EditMatchProfile::route('/{record}/edit')];
    }
}
