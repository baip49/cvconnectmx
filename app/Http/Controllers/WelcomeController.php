<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();
        $dashboardRoute = null;

        if ($user) {
            $dashboardRoute = match ($user->role?->name) {
                'admin' => '/admin',
                'company' => '/company',
                default => '/dashboard',
            };
        }

        return view('welcome', [
            'isAuthenticated' => $user !== null,
            'dashboardRoute' => $dashboardRoute,
        ]);
    }
}
