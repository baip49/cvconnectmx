<?php

namespace App\Http\Middleware;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Filament's panel authentication, but a user who opens another role's panel
 * (an old bookmark, a stale "intended" URL after login) is sent to their own panel instead of a 403.
 */
class RedirectToOwnPanel extends Authenticate
{
    /**
     * @param  array<string>  $guards
     */
    protected function authenticate($request, array $guards): void
    {
        $user = Filament::auth()->user();

        if ($user instanceof User
            && ! $user->canAccessPanel(Filament::getCurrentOrDefaultPanel())
            && ! $request->is(ltrim($user->panelPath(), '/').'*')
        ) {
            throw new HttpResponseException(redirect($user->panelPath()));
        }

        parent::authenticate($request, $guards);
    }
}
