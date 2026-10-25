<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;
use Symfony\Component\HttpFoundation\Response;

class VerifyEmailResponse implements VerifyEmailResponseContract
{
    /**
     * Redirect the verified user to the panel of their role.
     *
     * @param  Request  $request
     */
    public function toResponse($request): Response
    {
        return redirect()->intended($request->user()->panelPath().'?verified=1');
    }
}
