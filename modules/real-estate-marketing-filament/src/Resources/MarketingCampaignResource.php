<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MarketingFilament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\RealEstate\Marketing\Models\MarketingCampaign;
use Liberu\RealEstate\MarketingFilament\Resources\MarketingCampaignResource\Pages\CreateMarketingCampaign;
use Liberu\RealEstate\MarketingFilament\Resources\MarketingCampaignResource\Pages\EditMarketingCampaign;
use Liberu\RealEstate\MarketingFilament\Resources\MarketingCampaignResource\Pages\ListMarketingCampaigns;

final class MarketingCampaignResource extends Resource
{
    protected static ?string $model = MarketingCampaign::class;

    public static function form($form): mixed
    {
        return $form->schema([TextInput::make('name')->required()->maxLength(255), TextInput::make('channel')->required()->maxLength(80), TextInput::make('property_id')->numeric(), TextInput::make('listing_id')->numeric(), TextInput::make('status')->required(), Textarea::make('notes')->columnSpanFull()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('channel')->badge(), TextColumn::make('status')->badge(), TextColumn::make('created_at')->dateTime()])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListMarketingCampaigns::route('/'), 'create' => CreateMarketingCampaign::route('/create'), 'edit' => EditMarketingCampaign::route('/{record}/edit')];
    }
}
