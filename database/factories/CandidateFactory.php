<?php

namespace Database\Factories;

use App\Enums\Sex;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidate>
 */
class CandidateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'phone' => fake()->phoneNumber(),
            'age' => fake()->numberBetween(18, 65),
            'sex' => fake()->randomElement(['M', 'F']),
            'address' => fake()->address(),
            'scoring' => fake()->numberBetween(0, 100),
        ];
    }
}
