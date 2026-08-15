<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages;
use App\Filament\App\Pages\EditProfile;
use App\Filament\App\Pages\Tenant\Profile;
use App\Http\Middleware\AssignDefaultTeam;
use App\Http\Middleware\TeamsPermission;
use App\Models\Team;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use JoelButcher\Socialstream\Filament\SocialstreamPlugin;
use Laravel\Jetstream\Features;

class AgentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel
            ->id('agent')
            ->path('agent')
            ->homeUrl('/agent')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Edit Profile')
                    ->icon('heroicon-o-user-circle')
                    ->url(fn () => $this->shouldRegisterMenuItem()
                        ? url(Pages\EditProfile::getUrl())
                        : url($panel->getPath())),
            ]);

        if (Features::hasTeamFeatures()) {
            $panel
                ->tenant(Team::class, ownershipRelationship: 'team')
                ->tenantMiddleware([
                    AssignDefaultTeam::class,
                ]);
        }

        $panel
            ->discoverResources(in: app_path('Filament/Agent/Resources'), for: 'App\\Filament\\Agent\\Resources')
            ->discoverPages(in: app_path('Filament/Agent/Pages'), for: 'App\\Filament\\Agent\\Pages')
            ->pages([
                Pages\EditProfile::class,
                Profile::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Agent/Widgets'), for: 'App\\Filament\\Agent\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                TeamsPermission::class,
            ])
            ->plugins([
                new SocialstreamPlugin(),
            ]);

        return $panel;
    }

    public function boot(): void
    {
        /*
         * Fortify::$registersRoutes and Jetstream::$registersRoutes used to be
         * switched off here, in nine panel providers. They are statics, and
         * this runs in boot() — after Fortify has already registered its
         * routes — so in a fresh process the flags changed nothing and the
         * public /login, /register and /forgot-password routes existed anyway.
         * In a process that boots the application twice, they vanished: every
         * test after the first in a file had no auth routes at all, which is
         * why nothing here has ever covered them, and a warm Octane worker
         * would have served 404 for the sign-in page.
         */
    }

    public function shouldRegisterMenuItem(): bool
    {
        return true;
    }
}
