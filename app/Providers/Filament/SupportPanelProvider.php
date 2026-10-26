<?php

namespace App\Providers\Filament;

use App\Filament\Support\Pages\Dashboard;
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

class SupportPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('support')
            ->path('support')
            ->navigationGroups([
                NavigationGroup::make()->label(fn (): string => __('support.navigation_groups.inicio')),
                NavigationGroup::make()->label(fn (): string => __('support.navigation_groups.tickets')),
            ])
            ->pages([
                Dashboard::class,
            ])
            ->discoverResources(in: app_path('Filament/Support/Resources'), for: 'App\Filament\Support\Resources')
            ->discoverPages(in: app_path('Filament/Support/Pages'), for: 'App\Filament\Support\Pages')
            ->discoverWidgets(in: app_path('Filament/Support/Widgets'), for: 'App\Filament\Support\Widgets')
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
                'role:support',
            ]);
    }
}
