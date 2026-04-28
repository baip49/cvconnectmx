<?php

namespace App\Actions\Fortify;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_type' => ['required', Rule::in(['candidate', 'company'])],
        ])->validate();

        $roleName = $input['user_type'] === 'company' ? 'company' : 'candidate';
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => ucfirst($roleName), 'active' => true]);

        $user = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => $input['name'],
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        if ($roleName === 'candidate') {
            Candidate::create(['user_id' => $user->id]);
        } else {
            Company::create([
                'user_id' => $user->id,
                'name' => $input['name'].' '.$input['last_name'],
            ]);
        }

        return $user;
    }
}
