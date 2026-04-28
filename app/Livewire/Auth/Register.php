<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\RegisterResponse;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';

    public string $last_name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $user_type = 'candidate';

    public function register(CreatesNewUsers $creator)
    {
        $input = [
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'user_type' => $this->user_type,
        ];

        $user = $creator->create($input);

        Auth::login($user);

        session()->regenerate();

        return app(RegisterResponse::class);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
