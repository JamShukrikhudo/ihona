<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PortalsReportingFilament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\RealEstate\PortalsReporting\Models\PortalReport;
use Liberu\RealEstate\PortalsReportingFilament\Resources\PortalReportResource\Pages\CreatePortalReport;
use Liberu\RealEstate\PortalsReportingFilament\Resources\PortalReportResource\Pages\EditPortalReport;
use Liberu\RealEstate\PortalsReportingFilament\Resources\PortalReportResource\Pages\ListPortalReports;

final class PortalReportResource extends Resource
{
    protected static ?string $model = PortalReport::class;

    public static function form($form): mixed
    {
        return $form->schema([TextInput::make('portal')->required()->maxLength(120), TextInput::make('report_type')->required()->maxLength(120), TextInput::make('property_id')->numeric(), TextInput::make('listing_id')->numeric(), TextInput::make('status')->required(), Textarea::make('error')->columnSpanFull()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('portal')->searchable(), TextColumn::make('report_type')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('generated_at')->dateTime()])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListPortalReports::route('/'), 'create' => CreatePortalReport::route('/create'), 'edit' => EditPortalReport::route('/{record}/edit')];
    }
}
