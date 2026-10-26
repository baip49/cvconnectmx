<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

/**
 * Profile and security (password, 2FA) for every panel, built from the Flux settings components.
 */
class AccountSettings extends Page
{
    protected string $view = 'filament.pages.account-settings';

    protected static ?string $slug = 'account';

    protected static bool $shouldRegisterNavigation = false;

    /**
     * Managing 2FA asks for the password again, as Fortify's "confirmPassword" option requires.
     */
    protected static string|array $routeMiddleware = 'password.confirm';

    public function getTitle(): string
    {
        return __('shared.account.title');
    }
}
