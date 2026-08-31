<?php

namespace App\Providers\Filament;

use App\Filament\ModulePlugins;
use App\Http\Middleware\ApplyTeamIntegrationSettings;
use App\Support\ThemeColors;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Liberu\Foundation\ApplicationCore\Http\Middleware\SecurityHeaders;
use Liberu\Foundation\Localization\Http\Middleware\SetLocale;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('app')
            ->colors(app(ThemeColors::class)->forSite())
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\Filament\App\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\Filament\App\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->navigationGroups([
                'Explore',
                'Account',
            ])
            ->navigationItems([
                NavigationItem::make('News')
                    ->url(fn (): string => route('news.list'))
                    ->icon('heroicon-o-newspaper')
                    ->group('Explore'),
                NavigationItem::make('Saved properties')
                    ->url(fn (): string => route('wishlist'))
                    ->icon('heroicon-o-heart')
                    ->group('Explore'),
                NavigationItem::make('Calculators')
                    ->url(fn (): string => route('calculators'))
                    ->icon('heroicon-o-calculator')
                    ->group('Explore'),
                NavigationItem::make('Contact support')
                    ->url(fn (): string => route('contact.show'))
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->group('Explore'),
                NavigationItem::make('Profile')
                    ->url(fn (): string => route('profile.show'))
                    ->icon('heroicon-o-user-circle')
                    ->group('Account'),
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\Filament\App\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ApplyTeamIntegrationSettings::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetLocale::class,
                SecurityHeaders::class,
            ])
            ->plugins(app(ModulePlugins::class)->forPanel('app'))
            ->bootUsing(fn (Panel $panel): null => NavigationGroups::configure($panel))
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
