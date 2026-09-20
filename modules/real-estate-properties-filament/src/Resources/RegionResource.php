<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\RealEstate\Properties\Models\Region;
use Liberu\RealEstate\PropertiesFilament\Resources\RegionResource\Pages\CreateRegion;
use Liberu\RealEstate\PropertiesFilament\Resources\RegionResource\Pages\EditRegion;
use Liberu\RealEstate\PropertiesFilament\Resources\RegionResource\Pages\ListRegions;

/**
 * Global reference data (not team-scoped) — a region is a fact about
 * geography, not about which team lists properties in it.
 */
final class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.region.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.region.plural');
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static string|\UnitEnum|null $navigationGroup = 'Настройка объектов';

    public static function isScopedToTenant(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('filament.region.sections.details'))
                ->description(__('filament.region.sections.details_description'))
                ->columns(2)
                ->schema([
                    TextInput::make('name')->label(__('filament.region.fields.name'))->required()->maxLength(120),
                    TextInput::make('slug')->label(__('filament.region.fields.slug'))->maxLength(140)->helperText('Leave blank to derive from the name.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label(__('filament.region.fields.name'))->searchable()->sortable(),
            TextColumn::make('slug')->label(__('filament.region.fields.slug'))->searchable(),
            TextColumn::make('cities_count')->label(__('filament.resources.city.plural'))->counts('cities'),
            TextColumn::make('created_at')->label(__('filament.region.fields.created_at'))->dateTime()->sortable(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRegions::route('/'),
            'create' => CreateRegion::route('/create'),
            'edit' => EditRegion::route('/{record}/edit'),
        ];
    }
}
