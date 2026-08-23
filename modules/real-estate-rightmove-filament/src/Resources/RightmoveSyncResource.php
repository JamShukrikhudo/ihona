<?php

declare(strict_types=1);

namespace Liberu\RealEstate\RightmoveFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\RealEstate\Rightmove\Models\RightmoveSync;
use Liberu\RealEstate\RightmoveFilament\Resources\RightmoveSyncResource\Pages\CreateRightmoveSync;
use Liberu\RealEstate\RightmoveFilament\Resources\RightmoveSyncResource\Pages\EditRightmoveSync;
use Liberu\RealEstate\RightmoveFilament\Resources\RightmoveSyncResource\Pages\ListRightmoveSyncs;

final class RightmoveSyncResource extends Resource
{
    protected static ?string $model = RightmoveSync::class;

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
        return ['index' => ListRightmoveSyncs::route('/'), 'create' => CreateRightmoveSync::route('/create'), 'edit' => EditRightmoveSync::route('/{record}/edit')];
    }
}
