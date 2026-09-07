<?php

namespace App\Providers\Filament;

use App\Filament\Candidate\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class CandidatePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->renderHook(
                PanelsRenderHook::PAGE_HEADER_WIDGETS_BEFORE,
                fn () => view('filament.candidate.components.blocked-banner')
            )
            ->id('candidate')
            ->path('dashboard')
            ->brandName('CVConnectMX')
            ->brandLogo(asset('images/candidate-logo.svg'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('favicon.ico'))
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                'Inicio',
                'Perfil',
                'Oportunidades',
            ])
            ->pages([
                Dashboard::class,
            ])
            ->discoverResources(in: app_path('Filament/Candidate/Resources'), for: 'App\Filament\Candidate\Resources')
            ->discoverPages(in: app_path('Filament/Candidate/Pages'), for: 'App\Filament\Candidate\Pages')
            ->discoverWidgets(in: app_path('Filament/Candidate/Widgets'), for: 'App\Filament\Candidate\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                'role:candidate',
            ]);
    }
}
