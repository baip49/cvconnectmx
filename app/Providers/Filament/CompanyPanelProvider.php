<?php

namespace App\Providers\Filament;

use App\Http\Middleware\RedirectToOwnPanel;
use App\Http\Middleware\SetLocale;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class CompanyPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('company')
            ->path('company')
            ->navigationGroups([
                NavigationGroup::make()->label(fn (): string => __('company.navigation_groups.inicio')),
                NavigationGroup::make()->label(fn (): string => __('company.navigation_groups.gestion')),
                NavigationGroup::make()->label(fn (): string => __('company.navigation_groups.busqueda')),
                NavigationGroup::make()->label(fn (): string => __('company.navigation_groups.soporte')),
            ])
            ->discoverResources(in: app_path('Filament/Company/Resources'), for: 'App\Filament\Company\Resources')
            ->discoverPages(in: app_path('Filament/Company/Pages'), for: 'App\Filament\Company\Pages')
            ->discoverWidgets(in: app_path('Filament/Company/Widgets'), for: 'App\Filament\Company\Widgets')
            ->widgets([
                // Los widgets se descubren automáticamente en app/Filament/Company/Widgets
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
                'role:company',
            ]);
    }
}
