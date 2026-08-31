<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\RealEstate\PropertyManagement\Models\VendorQuote;
use Liberu\RealEstate\PropertyManagementFilament\Resources\VendorQuoteResource\Pages\CreateVendorQuote;
use Liberu\RealEstate\PropertyManagementFilament\Resources\VendorQuoteResource\Pages\EditVendorQuote;
use Liberu\RealEstate\PropertyManagementFilament\Resources\VendorQuoteResource\Pages\ListVendorQuotes;

final class VendorQuoteResource extends Resource
{
    protected static ?string $model = VendorQuote::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-currency-dollar';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('vendor_id')->required()->numeric(), TextInput::make('property_id')->required()->numeric(), Textarea::make('work_description')->required()->columnSpanFull(), TextInput::make('quote_amount')->required()->numeric()->minValue(0), DatePicker::make('quote_date')->required(), DatePicker::make('valid_until')->required(), Select::make('status')->options(['pending' => 'Pending', 'accepted' => 'Accepted', 'rejected' => 'Rejected', 'expired' => 'Expired', 'withdrawn' => 'Withdrawn'])->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('vendor_id')->sortable(), TextColumn::make('property_id')->sortable(), TextColumn::make('quote_amount')->money()->sortable(), TextColumn::make('status')->badge(), TextColumn::make('valid_until')->date()->sortable()])->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $team = auth()->user()?->current_team_id;
        return parent::getEloquentQuery()->when($team === null, fn (Builder $q) => $q->whereRaw('1=0'), fn (Builder $q) => $q->forTeam($team));
    }

    public static function getPages(): array
    {
        return ['index' => ListVendorQuotes::route('/'), 'create' => CreateVendorQuote::route('/create'), 'edit' => EditVendorQuote::route('/{record}/edit')];
    }
}
