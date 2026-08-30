<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesFilament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
//use Filament\Schemas\Components\Select;
//use Filament\Schemas\Components\Toggle;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyResource\Pages\CreateProperty;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyResource\Pages\EditProperty;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyResource\Pages\ListProperties;

final class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|\UnitEnum|null $navigationGroup = 'Недвижимость';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->maxLength(255),
            Textarea::make('address')->required()->columnSpanFull(),
            Textarea::make('description')->columnSpanFull(),
            TextInput::make('price')->numeric()->minValue(0),
            TextInput::make('currency')->length(3)->default('TJS'),
            TextInput::make('bedrooms')->numeric()->minValue(0),
            TextInput::make('bathrooms')->numeric()->minValue(0),
            TextInput::make('area_sqft')->numeric()->minValue(0),
            TextInput::make('year_built')->numeric()->minValue(1066)->maxValue((int) now()->year + 2),
            Select::make('property_type')
                ->label('Тип недвижимости')
                ->options([
                    'apartment' => 'Квартира',
                    'house' => 'Дом',
                    'guesthouse' => 'Гестхаус', 
                    'hostel' => 'Хостел',
                    'land' => 'Земельный участок',
                    'commercial' => 'Коммерческая',
                    'cottage' => 'Дача',
                ])
                ->nullable(),
            TextInput::make('postal_code')->maxLength(20),
            TextInput::make('country')->length(2),
            TextInput::make('tenure')->maxLength(40),
            TextInput::make('virtual_tour_url')->url()->maxLength(2048),
            TextInput::make('model_3d_url')->url()->maxLength(2048),
            Section::make('🏔️ Региональные особенности')
                ->schema([
                    Toggle::make('has_generator')
                        ->label('Генератор')
                        ->default(false),
                    Toggle::make('has_wifi')
                        ->label('Wi-Fi')
                        ->default(false),
                    Toggle::make('has_parking')
                        ->label('Парковка')
                        ->default(false),
                    Select::make('mountain_view')
                        ->label('Вид на горы')
                        ->options([
                            'pamir' => 'Памир',
                            'fan' => 'Фанские горы',
                            'hissar' => 'Гиссарский хребет',
                            'other' => 'Другие',
                        ])
                        ->nullable(),
                    TextInput::make('altitude')
                        ->label('Высота над уровнем моря (м)')
                        ->numeric()
                        ->nullable(),
                    Select::make('water_source')
                        ->label('Источник воды')
                        ->options([
                            'well' => 'Скважина',
                            'river' => 'Река',
                            'spring' => 'Родник',
                            'other' => 'Другой',
                        ])
                        ->nullable(),
                    TextInput::make('max_guests')
                        ->label('Максимальное количество гостей')
                        ->numeric()
                        ->minValue(1)
                        ->nullable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                $teamId = auth()->user()?->current_team_id;

                return $teamId === null ? $query->whereRaw('1 = 0') : $query->forTeam($teamId);
            })
            ->columns([
                TextColumn::make('address')->searchable()->wrap(),
                TextColumn::make('property_type')->label('Type')->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = auth()->user()?->current_team_id;

        return parent::getEloquentQuery()->when(
            $teamId === null,
            fn (Builder $query): Builder => $query->whereRaw('1 = 0'),
            fn (Builder $query): Builder => $query->forTeam($teamId),
        );
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return [
            'index' => ListProperties::route('/'),
            'create' => CreateProperty::route('/create'),
            'edit' => EditProperty::route('/{record}/edit'),
        ];
    }
}
