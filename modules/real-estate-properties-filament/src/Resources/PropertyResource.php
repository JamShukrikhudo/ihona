<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesFilament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\RealEstate\Core\Models\Branch;
use Liberu\RealEstate\Properties\Application\RecordPropertyKey;
use Liberu\RealEstate\Properties\Application\TransitionProperty;
use Liberu\RealEstate\Properties\Application\UpsertPropertyUnit;
use Liberu\RealEstate\Properties\Domain\PropertyStatus;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\Properties\Models\PropertyCategory;
use Liberu\RealEstate\Properties\Models\PropertyTemplate;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyResource\Pages\CreateProperty;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyResource\Pages\EditProperty;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyResource\Pages\ListProperties;

final class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|\UnitEnum|null $navigationGroup = 'Real Estate';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->maxLength(255),
            Select::make('status')->options(collect(PropertyStatus::cases())->mapWithKeys(fn (PropertyStatus $status): array => [$status->value => str($status->value)->headline()->toString()])->all())->disabled()->dehydrated(false),
            Textarea::make('address')->required()->columnSpanFull(),
            Select::make('branch_id')
                ->label('Branch')
                ->options(fn (): array => Branch::query()
                    ->forTeam(auth()->user()?->current_team_id ?? 0)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all())
                ->searchable()
                ->nullable(),
            Textarea::make('description')->columnSpanFull(),
            Textarea::make('internal_notes')->label('Internal notes')->columnSpanFull(),
            TextInput::make('price')->numeric()->minValue(0),
            TextInput::make('currency')->length(3)->default('GBP'),
            TextInput::make('bedrooms')->numeric()->minValue(0),
            TextInput::make('bathrooms')->numeric()->minValue(0),
            TextInput::make('reception_rooms')->numeric()->minValue(0),
            TextInput::make('area_sqft')->numeric()->minValue(0),
            TextInput::make('year_built')
                ->numeric()
                ->minValue(Property::EARLIEST_YEAR_BUILT)
                ->maxValue(Property::latestYearBuilt())
                ->helperText(Property::yearBuiltMessage()),
            Select::make('property_type')->options(Property::TYPES)->required(),
            Select::make('property_category_id')
                ->label('Category')
                ->options(fn (): array => PropertyCategory::query()->forTeam(auth()->user()?->current_team_id ?? 0)->orderBy('name')->pluck('name', 'id')->all())
                ->searchable()
                ->nullable(),
            Select::make('property_template_id')
                ->label('Listing template')
                ->options(fn (): array => PropertyTemplate::query()->forTeam(auth()->user()?->current_team_id ?? 0)->orderBy('name')->pluck('name', 'id')->all())
                ->searchable()
                ->nullable(),
            TextInput::make('postal_code')->maxLength(20),
            TextInput::make('country')->length(2),
            TextInput::make('tenure')->maxLength(40),
            TextInput::make('council_tax_band')->maxLength(10),
            TextInput::make('energy_rating')->maxLength(10),
            TextInput::make('energy_score')->numeric()->minValue(0)->maxValue(100),
            TextInput::make('walkability_score')->numeric()->minValue(0)->maxValue(100),
            TextInput::make('transit_score')->numeric()->minValue(0)->maxValue(100),
            TextInput::make('bike_score')->numeric()->minValue(0)->maxValue(100),
            TextInput::make('virtual_tour_url')->url()->maxLength(2048),
            TextInput::make('virtual_tour_provider')->maxLength(40),
            Toggle::make('live_tour_available'),
            TextInput::make('model_3d_url')->url()->maxLength(2048),
            TextInput::make('floor_plan_image')->url()->maxLength(2048),
            Toggle::make('is_featured'),
            Toggle::make('ar_tour_enabled'),
            Toggle::make('holographic_enabled'),
            TextInput::make('holographic_tour_url')->url()->maxLength(2048),
            TextInput::make('holographic_provider')->maxLength(255),
            TagsInput::make('features')->separator(','),
            TextInput::make('insurance_policy_id')->numeric()->minValue(1),
            TextInput::make('insurance_coverage_amount')->numeric()->minValue(0),
            TextInput::make('insurance_premium')->numeric()->minValue(0),
            TextInput::make('insurance_expiry_date')->date(),
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
            ->filters([
                Filter::make('minimum_scores')
                    ->form([
                        TextInput::make('energy_score')->numeric()->minValue(0)->maxValue(100),
                        TextInput::make('walkability_score')->numeric()->minValue(0)->maxValue(100),
                        TextInput::make('transit_score')->numeric()->minValue(0)->maxValue(100),
                        TextInput::make('bike_score')->numeric()->minValue(0)->maxValue(100),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->minEnergyScore($data['energy_score'] ?? null)
                        ->walkabilityScore($data['walkability_score'] ?? null)
                        ->transitScore($data['transit_score'] ?? null)
                        ->bikeScore($data['bike_score'] ?? null)),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('favorite')
                    ->label('Toggle favorite')
                    ->action(fn (Property $record): bool => app(\Liberu\RealEstate\Properties\Application\TogglePropertyFavorite::class)->handle($record->team_id, auth()->id(), $record->getKey())),
                Action::make('similar')
                    ->label('Similar properties')
                    ->action(function (Property $record): void {
                        Notification::make()
                            ->title($record->similarProperties()->count().' similar properties found')
                            ->success()
                            ->send();
                    }),
                Action::make('unit')->form([TextInput::make('label')->required()->maxLength(80), TextInput::make('bedrooms')->numeric()->minValue(0), TextInput::make('bathrooms')->numeric()->minValue(0), TextInput::make('area_sqft')->numeric()->minValue(0)])->action(fn (Property $record, array $data): mixed => app(UpsertPropertyUnit::class)->handle($record, (int) auth()->user()->current_team_id, $data)),
                Action::make('key')->form([TextInput::make('key_reference')->required()->maxLength(80), TextInput::make('quantity')->numeric()->required()->minValue(1), Textarea::make('notes')])->action(fn (Property $record, array $data): mixed => app(RecordPropertyKey::class)->handle($record, (int) auth()->user()->current_team_id, $data)),
                Action::make('available')
                    ->label('Publish')
                    ->action(fn (Property $record): Property => app(TransitionProperty::class)->handle($record->team_id, auth()->id(), $record->getKey(), PropertyStatus::Available))
                    ->visible(fn (Property $record): bool => $record->status === PropertyStatus::Draft),
                Action::make('under_offer')
                    ->label('Mark under offer')
                    ->action(fn (Property $record): Property => app(TransitionProperty::class)->handle($record->team_id, auth()->id(), $record->getKey(), PropertyStatus::UnderOffer))
                    ->visible(fn (Property $record): bool => $record->status === PropertyStatus::Available),
                Action::make('sold')
                    ->label('Mark sold')
                    ->action(fn (Property $record): Property => app(TransitionProperty::class)->handle($record->team_id, auth()->id(), $record->getKey(), PropertyStatus::Sold))
                    ->visible(fn (Property $record): bool => in_array($record->status, [PropertyStatus::Available, PropertyStatus::UnderOffer], true)),
                Action::make('withdraw')
                    ->label('Withdraw')
                    ->action(fn (Property $record): Property => app(TransitionProperty::class)->handle($record->team_id, auth()->id(), $record->getKey(), PropertyStatus::Withdrawn))
                    ->visible(fn (Property $record): bool => in_array($record->status, [PropertyStatus::Draft, PropertyStatus::Available, PropertyStatus::UnderOffer], true)),
                DeleteAction::make(),
            ])
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
