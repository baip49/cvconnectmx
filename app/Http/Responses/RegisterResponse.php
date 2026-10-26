<?php

namespace App\Http\Responses;

use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Redirect the user to the panel of their role.
     *
     * @param  Request  $request
     */
    public function toResponse($request): Response
    {
        Notification::make()->title(__('shared.auth.registered'))->success()->send();

        return redirect()->intended($request->user()->panelPath());
    }
}
