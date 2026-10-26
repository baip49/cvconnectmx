<?php

namespace App\Http\Responses;

use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    /**
     * Redirect the user to the panel of their role.
     *
     * @param  Request  $request
     */
    public function toResponse($request): Response
    {
        Notification::make()->title(__('shared.auth.logged_in'))->success()->send();

        return redirect()->intended($request->user()->panelPath());
    }
}
