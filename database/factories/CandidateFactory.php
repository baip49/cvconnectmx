<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidateFactory extends Factory
{
    protected $model = Candidate::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'professional_title' => $this->faker->jobTitle(),
            'summary' => $this->faker->paragraph(),
            'city' => $this->faker->city(),
            'expected_salary' => $this->faker->randomFloat(2, 10000, 50000),
            'phone_encrypted' => $this->faker->phoneNumber(),
            'is_public_profile' => true,
        ];
    }
}
