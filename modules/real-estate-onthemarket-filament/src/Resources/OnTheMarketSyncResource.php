<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OnTheMarketFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\RealEstate\OnTheMarket\Models\OnTheMarketSync;
use Liberu\RealEstate\OnTheMarketFilament\Resources\OnTheMarketSyncResource\Pages\CreateOnTheMarketSync;
use Liberu\RealEstate\OnTheMarketFilament\Resources\OnTheMarketSyncResource\Pages\EditOnTheMarketSync;
use Liberu\RealEstate\OnTheMarketFilament\Resources\OnTheMarketSyncResource\Pages\ListOnTheMarketSyncs;

final class OnTheMarketSyncResource extends Resource
{
    protected static ?string $model = OnTheMarketSync::class;

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
        return ['index' => ListOnTheMarketSyncs::route('/'), 'create' => CreateOnTheMarketSync::route('/create'), 'edit' => EditOnTheMarketSync::route('/{record}/edit')];
    }
}
