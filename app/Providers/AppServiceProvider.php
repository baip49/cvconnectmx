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
use Filament\Infolists\Components\TextEntry;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
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
        $this->configureVerifiedBadge();

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
     * Every Filament column or entry showing a user's or company's name ("name", "company.name", "candidate.user.name"...)
     * gets the blue check when that account is verified, including columns added later.
     */
    protected function configureVerifiedBadge(): void
    {
        $addBadge = function (TextColumn|TextEntry $component): void {
            $name = $component->getName();

            if ($name !== 'name' && ! str_ends_with($name, '.name')) {
                return;
            }

            // shortcut: resolves the owner per row (one query for company.user), eager load it if a list grows past a few hundred rows.
            $isVerified = function (?Model $record) use ($name): bool {
                $owner = str_contains($name, '.') ? data_get($record, Str::beforeLast($name, '.')) : $record;

                return ($owner instanceof User || $owner instanceof Company) && $owner->isVerifiedAccount();
            };

            $component
                ->icon(fn (?Model $record): ?Heroicon => $isVerified($record) ? Heroicon::CheckBadge : null)
                ->iconColor('primary')
                ->iconPosition(IconPosition::After)
                ->tooltip(fn (?Model $record): ?string => $isVerified($record) ? __('shared.verification.badge') : null);
        };

        TextColumn::configureUsing($addBadge);
        TextEntry::configureUsing($addBadge);
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
