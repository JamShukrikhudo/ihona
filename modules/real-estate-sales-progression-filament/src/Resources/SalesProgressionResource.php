<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgressionFilament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\RealEstate\SalesProgression\Models\SalesProgression;
use Liberu\RealEstate\SalesProgressionFilament\Resources\SalesProgressionResource\Pages\CreateSalesProgression;
use Liberu\RealEstate\SalesProgressionFilament\Resources\SalesProgressionResource\Pages\EditSalesProgression;
use Liberu\RealEstate\SalesProgressionFilament\Resources\SalesProgressionResource\Pages\ListSalesProgressions;

final class SalesProgressionResource extends Resource
{
    protected static ?string $model = SalesProgression::class;

    public static function form($form): mixed
    {
        return $form->schema([TextInput::make('subject')->required()->maxLength(255), TextInput::make('property_id')->numeric(), TextInput::make('offer_id')->numeric(), TextInput::make('status')->required(), Textarea::make('notes')->columnSpanFull()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('subject')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('created_at')->dateTime()])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListSalesProgressions::route('/'), 'create' => CreateSalesProgression::route('/create'), 'edit' => EditSalesProgression::route('/{record}/edit')];
    }
}
