<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'uuid' => Str::uuid(),
            'role_id' => fn () => Role::firstOrCreate(['name' => 'candidate'], ['description' => 'Candidate role', 'active' => true])->id,
            'name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): self
    {
        return $this->state(function () {
            return [
                'role_id' => Role::firstOrCreate(
                    ['name' => 'admin'],
                    ['description' => 'Administrator role', 'active' => true]
                )->id,
                'is_active' => true,
            ];
        });
    }

    public function company(): self
    {
        return $this->state(function () {
            return [
                'role_id' => Role::firstOrCreate(
                    ['name' => 'company'],
                    ['description' => 'Company role', 'active' => true]
                )->id,
            ];
        })->afterCreating(function (User $user) {
            $user->company()->create([
                'name' => $user->name.' Enterprise',
                'sector' => 'Technology',
                'city' => 'Tuxtla Gutiérrez',
                'state' => 'Chiapas',
            ]);
        });
    }

    public function candidate(): self
    {
        return $this->state(function () {
            return [
                'role_id' => Role::firstOrCreate(
                    ['name' => 'candidate'],
                    ['description' => 'Candidate role', 'active' => true]
                )->id,
            ];
        })->afterCreating(function (User $user) {
            $user->candidate()->create([
                'professional_title' => 'Candidato Jr',
                'summary' => 'Candidato de prueba para CVConnectMX',
                'phone_encrypted' => '9611234567',
                'city' => 'Tuxtla Gutiérrez',
            ]);
        });
    }

    public function inactive(): self
    {
        return $this->state([
            'is_active' => false,
        ]);
    }

    public function locked(): self
    {
        return $this->state([
            'locked_until' => now()->addHours(2),
            'failed_login_attempts' => 5,
        ]);
    }
}
