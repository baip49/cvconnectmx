<?php

namespace App\Providers\Filament;

use App\Filament\Candidate\Pages\Dashboard;
use App\Http\Middleware\RedirectToOwnPanel;
use App\Http\Middleware\SetLocale;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
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
            ->navigationGroups([
                NavigationGroup::make()->label(fn (): string => __('candidate.navigation_groups.inicio')),
                NavigationGroup::make()->label(fn (): string => __('candidate.navigation_groups.perfil')),
                NavigationGroup::make()->label(fn (): string => __('candidate.navigation_groups.oportunidades')),
                NavigationGroup::make()->label(fn (): string => __('candidate.navigation_groups.soporte')),
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
                SetLocale::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                RedirectToOwnPanel::class,
                'verified',
                'role:candidate',
            ]);
    }
}
