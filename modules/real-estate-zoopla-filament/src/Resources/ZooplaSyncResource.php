<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ZooplaFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\RealEstate\Zoopla\Models\ZooplaSync;
use Liberu\RealEstate\ZooplaFilament\Resources\ZooplaSyncResource\Pages\CreateZooplaSync;
use Liberu\RealEstate\ZooplaFilament\Resources\ZooplaSyncResource\Pages\EditZooplaSync;
use Liberu\RealEstate\ZooplaFilament\Resources\ZooplaSyncResource\Pages\ListZooplaSyncs;

final class ZooplaSyncResource extends Resource
{
    protected static ?string $model = ZooplaSync::class;

    public static function form($form): mixed
    {
        return $form->schema([TextInput::make('listing_id')->required()->numeric(), TextInput::make('property_id')->numeric(), TextInput::make('external_id'), TextInput::make('status')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('listing_id')->searchable(), TextColumn::make('external_id')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('last_synced_at')->dateTime()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListZooplaSyncs::route('/'), 'create' => CreateZooplaSync::route('/create'), 'edit' => EditZooplaSync::route('/{record}/edit')];
    }
}
