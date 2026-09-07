<?php

namespace Database\Factories;

use App\Models\PasswordHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PasswordHistory>
 */
class PasswordHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'password_hash' => bcrypt('password'),
        ];
    }
}
