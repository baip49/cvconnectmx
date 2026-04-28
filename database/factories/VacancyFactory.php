<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;

class VacancyFactory extends Factory
{
    protected $model = Vacancy::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'title' => $this->faker->jobTitle(),
            'description' => $this->faker->paragraphs(3, true),
            'requirements' => $this->faker->paragraphs(2, true),
            'work_model' => $this->faker->randomElement(['remote', 'hybrid', 'on_site']),
            'min_salary' => $this->faker->randomFloat(2, 5000, 15000),
            'max_salary' => $this->faker->randomFloat(2, 16000, 45000),
            'show_salary' => true,
            'status' => 'published',
            'published_at' => now(),
            'expires_at' => now()->addDays(30),
        ];
    }
}
