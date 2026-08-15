<?php

namespace App\Filament\Admin\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Pages\SettingsPage;

class ManageGeneralSettings extends SettingsPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GeneralSettings::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    protected static ?string $title = 'General Settings';

    protected static ?string $navigationLabel = 'General Settings';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Site Information')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Site Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('site_email')
                            ->label('Site Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('site_phone')
                            ->label('Site Phone')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('site_address')
                            ->label('Site Address')
                            ->maxLength(255),
                        TextInput::make('site_country')
                            ->label('Country')
                            ->maxLength(255),
                        Select::make('site_currency')
                            ->label('Currency')
                            ->helperText('Used wherever there is no listing to read a currency from, such as a filter chip.')
                            ->options(\App\Support\Currency::options())
                            ->default(\App\Support\Currency::DEFAULT)
                            ->required(),
                        TextInput::make('site_default_language')
                            ->label('Default Language')
                            ->maxLength(10)
                            ->default('en'),
                    ])
                    ->columns(2),

                Section::make('Social Media Links')
                    ->description('Add your social media profile URLs')
                    ->schema([
                        TextInput::make('facebook_url')
                            ->label('Facebook URL')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('twitter_url')
                            ->label('Twitter URL')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('github_url')
                            ->label('GitHub URL')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('youtube_url')
                            ->label('YouTube URL')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Registration and redress')
                    ->description('Published in the footer of every public page. Leave anything the agency does not hold blank and it is left out entirely.')
                    ->schema([
                        TextInput::make('company_registration_number')
                            ->label('Company registration number')
                            ->helperText('Companies House number. Leave blank for a sole trader.')
                            ->maxLength(50),
                        TextInput::make('ico_registration_number')
                            ->label('ICO registration number')
                            ->helperText('Data protection register entry, e.g. ZA123456.')
                            ->maxLength(50),
                        TextInput::make('vat_number')
                            ->label('VAT number')
                            ->helperText('Leave blank if the business is under the threshold.')
                            ->maxLength(50),
                        TextInput::make('redress_scheme')
                            ->label('Redress scheme')
                            ->helperText('The scheme the agency belongs to, e.g. The Property Ombudsman.')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Footer')
                    ->schema([
                        Textarea::make('footer_copyright')
                            ->label('Copyright Text')
                            ->required()
                            ->maxLength(500)
                            ->rows(2),
                    ]),
            ]);
    }
}