<?php

namespace App\Filament\App\Pages;

use App\Models\User;
use App\Support\TeamIntegrationSettings;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Liberu\Foundation\Organizations\Models\Team;
use Liberu\Foundation\Settings\Contracts\SettingDefinition;
use Liberu\Foundation\Settings\Services\ScopedSettings;

class AccountSetupWizard extends Page
{
    use InteractsWithForms;

    protected string $view = 'filament.app.pages.account-setup-wizard';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|\UnitEnum|null $navigationGroup = 'Account';

    protected static ?string $navigationLabel = 'Setup guide';

    protected static ?string $title = 'Get your workspace ready';

    protected static ?string $slug = 'setup';

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount(): void
    {
        $team = $this->team();
        $settings = app(ScopedSettings::class)->resolve(
            'team.integrations',
            ['team' => $team?->getKey()],
            [],
        );

        $this->form->fill([
            'team_name' => $team?->name,
            'openai_api_key' => '',
            'walkscore_api_key' => '',
            'rightmove_api_key' => '',
            'zoopla_api_key' => '',
            'onthemarket_api_key' => '',
            'google_analytics_id' => $settings['google_analytics_id'] ?? '',
            'meta_pixel_id' => $settings['meta_pixel_id'] ?? '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Wizard::make([
                    Step::make('Workspace')
                        ->description('Give your team a clear home')
                        ->schema([
                            TextInput::make('team_name')
                                ->label('Workspace name')
                                ->helperText('This is shown to you and your team members.')
                                ->required()
                                ->maxLength(255),
                        ]),
                    Step::make('Connections')
                        ->description('Add optional service credentials')
                        ->schema([
                            TextInput::make('openai_api_key')
                                ->label('OpenAI API key')
                                ->password()
                                ->revealable()
                                ->helperText('Needed for AI-powered property descriptions and summaries.'),
                            TextInput::make('walkscore_api_key')
                                ->label('Walk Score API key')
                                ->password()
                                ->revealable()
                                ->helperText('Needed for walkability scores.'),
                            TextInput::make('rightmove_api_key')->label('Rightmove API key')->password()->revealable(),
                            TextInput::make('zoopla_api_key')->label('Zoopla API key')->password()->revealable(),
                            TextInput::make('onthemarket_api_key')->label('OnTheMarket API key')->password()->revealable(),
                        ])
                        ->columns(2),
                    Step::make('Tracking')
                        ->description('Connect optional analytics')
                        ->schema([
                            TextInput::make('google_analytics_id')
                                ->label('Google Analytics measurement ID')
                                ->placeholder('G-XXXXXXXXXX'),
                            TextInput::make('meta_pixel_id')
                                ->label('Meta Pixel ID'),
                        ])
                        ->columns(2),
                    Step::make('Review')
                        ->description('Confirm your setup')
                        ->schema([
                            TextInput::make('setup_note')
                                ->label('Ready to go')
                                ->default('Your workspace is ready. You can update these settings any time.')
                                ->disabled()
                                ->dehydrated(false),
                        ]),
                ]),
            ]);
    }

    public function save(): void
    {
        $team = $this->team();

        if (! $team instanceof Team) {
            Notification::make()->title('Create a team before continuing.')->warning()->send();

            return;
        }

        if ((int) $team->user_id !== (int) auth()->id()) {
            Notification::make()->title('Only the team owner can finish workspace setup.')->warning()->send();

            return;
        }

        $state = $this->form->getState();
        $team->update(['name' => $state['team_name']]);

        $scopedSettings = app(ScopedSettings::class);
        $existing = (array) $scopedSettings->resolve('team.integrations', ['team' => $team->getKey()], []);
        $credentials = collect([
            'openai_api_key' => $state['openai_api_key'] ?? null,
            'walkscore_api_key' => $state['walkscore_api_key'] ?? null,
            'rightmove_api_key' => $state['rightmove_api_key'] ?? null,
            'zoopla_api_key' => $state['zoopla_api_key'] ?? null,
            'onthemarket_api_key' => $state['onthemarket_api_key'] ?? null,
            'google_analytics_id' => $state['google_analytics_id'] ?? null,
            'meta_pixel_id' => $state['meta_pixel_id'] ?? null,
        ])->filter(fn (mixed $value): bool => filled($value))->all();

        $scopedSettings->put(
            new TeamIntegrationSettings(),
            'team',
            (string) $team->getKey(),
            [...$existing, ...$credentials],
        );

        $scopedSettings->put(
            new class() implements SettingDefinition
            {
                public function key(): string
                {
                    return 'team.setup_completed';
                }

                public function validate(mixed $value): bool
                {
                    return is_bool($value);
                }

                public function secret(): bool
                {
                    return false;
                }
            },
            'team',
            (string) $team->getKey(),
            true,
        );

        Notification::make()->title('Workspace setup saved')->success()->send();
        $this->redirect(Filament::getPanel('app')->getUrl(), navigate: true);
    }

    /** @return Team|null */
    private function team(): ?Model
    {
        $user = auth()->user();

        return $user instanceof User ? $user->latestTeam : null;
    }

    /** @return array<string, bool> */
    public function oauthProviders(): array
    {
        return collect([
            'GitHub' => config('services.github.client_id'),
            'Google' => config('services.google.client_id'),
            'Facebook' => config('services.facebook.client_id'),
            'X / Twitter' => config('services.twitter-oauth-2.client_id'),
        ])->map(fn (mixed $clientId): bool => filled($clientId))->all();
    }

    public static function getNavigationBadge(): ?string
    {
        return self::isComplete() ? null : 'Start';
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public static function isComplete(): bool
    {
        $user = auth()->user();
        $team = $user instanceof User ? $user->latestTeam : null;

        return $team !== null && (bool) app(ScopedSettings::class)->resolve(
            'team.setup_completed',
            ['team' => $team->getKey()],
            false,
        );
    }
}
