<?php

namespace Database\Factories;

use App\Models\Training;
use App\Models\User;
use App\Models\UserTraining;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserTraining>
 */
class UserTrainingFactory extends Factory
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
            'training_id' => Training::factory(),
            'status' => $this->faker->randomElement(['assigned', 'in_progress', 'completed', 'expired']),
            'score' => $this->faker->optional()->randomFloat(2, 60, 100),
            'completed_at' => null,
            'expires_at' => now()->addDays(90),
        ];
    }
}
