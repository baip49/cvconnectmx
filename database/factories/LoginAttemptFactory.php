<?php

namespace Database\Factories;

use App\Models\LoginAttempt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoginAttempt>
 */
class LoginAttemptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->safeEmail(),
            'ip_address' => $this->faker->ipv4(),
            'is_successful' => $this->faker->boolean(75),
            'failure_reason' => $this->faker->optional()->randomElement(['invalid_credentials', 'locked_account', 'inactive_account']),
        ];
    }
}
