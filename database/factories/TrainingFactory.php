<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\Training;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Training>
 */
class TrainingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'type' => $this->faker->randomElement(['security', 'technical', 'compliance']),
            'target_role_id' => Role::query()->inRandomOrder()->value('id'),
            'validity_days' => $this->faker->randomElement([30, 90, 365]),
            'is_active' => true,
        ];
    }
}
