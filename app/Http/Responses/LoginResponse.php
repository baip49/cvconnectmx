<?php

namespace App\Http\Responses;

use Filament\Notifications\Notification;
use Flux\Flux;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request  $request
     * @return Response
     */
    public function toResponse($request)
    {
        $role = Auth::user()->role?->name ?? '';

        if ($role === 'candidate') {
            Flux::toast('Sesión iniciada correctamente.', variant: 'success');

            return redirect()->intended('/dashboard'); // Portal 1
        } elseif ($role === 'company') {
            Notification::make()->title('Sesión iniciada correctamente.')->success()->send();

            return redirect()->intended('/company'); // Portal 2
        } elseif ($role === 'admin') {
            Notification::make()->title('Sesión iniciada correctamente.')->success()->send();

            return redirect()->intended('/admin'); // Portal 3
        }

        return redirect()->intended(config('fortify.home'));
    }
}
