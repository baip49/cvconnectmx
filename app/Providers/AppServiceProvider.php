<?php

namespace App\Providers;

use App\Filament\AvatarProviders\InitialsAvatarProvider;
use App\Filament\Pages\AccountSettings;
use App\Models\Application;
use App\Models\BackupLog;
use App\Models\Candidate;
use App\Models\CandidateDocument;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use App\Models\Vacancy;
use App\Observers\ApplicationObserver;
use App\Observers\AuditObserver;
use App\Observers\UserObserver;
use Carbon\CarbonImmutable;
use Filament\FontProviders\LocalFontProvider;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\VerifyEmailResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            LoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );
        $this->app->singleton(
            RegisterResponse::class,
            \App\Http\Responses\RegisterResponse::class
        );
        $this->app->singleton(
            VerifyEmailResponse::class,
            \App\Http\Responses\VerifyEmailResponse::class
        );

        // Panels are built after this runs, so every panel starts from the shared brand.
        Panel::configureUsing(fn (Panel $panel): Panel => $panel
            ->viteTheme('resources/css/filament/theme.css')
            ->colors([
                'primary' => Color::Blue,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'danger' => Color::Red,
            ])
            ->font('Roboto', provider: LocalFontProvider::class)
            ->brandName('CVConnectMX')
            ->brandLogo(fn (): View => view('filament.components.brand-logo'))
            ->favicon(asset('logo.svg'))
            ->defaultAvatarProvider(InitialsAvatarProvider::class)
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->pages([AccountSettings::class])
            ->userMenuItems([
                'account' => MenuItem::make()
                    ->label(fn (): string => __('shared.account.title'))
                    ->url(fn (): string => AccountSettings::getUrl())
                    ->icon(Heroicon::OutlinedUserCircle),
                // Each language is named in itself, so it stays recognizable whatever the current one is.
                'locale' => MenuItem::make()
                    ->label(fn (): string => app()->getLocale() === 'en' ? 'Español' : 'English')
                    ->url(fn (): string => route('locale.switch', app()->getLocale() === 'en' ? 'es' : 'en'))
                    ->icon(Heroicon::OutlinedLanguage),
            ])
            ->renderHook(PanelsRenderHook::HEAD_START, fn (): View => view('partials.appearance-sync'))
            ->renderHook(PanelsRenderHook::BODY_END, fn (): string => Blade::render('@fluxScripts')));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        User::observe(AuditObserver::class);
        User::observe(UserObserver::class);
        Candidate::observe(AuditObserver::class);
        Company::observe(AuditObserver::class);
        Vacancy::observe(AuditObserver::class);
        Application::observe(AuditObserver::class);
        Role::observe(AuditObserver::class);
        Permission::observe(AuditObserver::class);
        CandidateDocument::observe(AuditObserver::class);
        Ticket::observe(AuditObserver::class);
        TicketReply::observe(AuditObserver::class);
        BackupLog::observe(AuditObserver::class);
        Application::observe(ApplicationObserver::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
