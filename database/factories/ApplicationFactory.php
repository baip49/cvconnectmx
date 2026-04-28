<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        return [
            'candidate_id' => Candidate::factory(),
            'vacancy_id' => Vacancy::factory(),
            'cover_letter' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'interview', 'accepted', 'rejected']),
            'rating' => $this->faker->randomFloat(2, 1, 5),
            'internal_notes' => $this->faker->sentence(),
        ];
    }
}
