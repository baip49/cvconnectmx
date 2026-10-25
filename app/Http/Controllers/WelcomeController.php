<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();

        return view('welcome', [
            'isAuthenticated' => $user !== null,
            'dashboardRoute' => $user?->panelPath(),
        ]);
    }
}
