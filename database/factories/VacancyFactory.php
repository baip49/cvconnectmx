<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Vacancy;
use Database\Factories\Concerns\HasSpanishContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class VacancyFactory extends Factory
{
    use HasSpanishContent;

    protected $model = Vacancy::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'title' => $this->spanish(self::$spanishJobTitles),
            'description' => $this->spanish(self::$spanishVacancyDescriptions),
            'requirements' => $this->spanish(self::$spanishVacancyRequirements),
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
