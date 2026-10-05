<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\WorkExperience;
use Database\Factories\Concerns\HasSpanishContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkExperienceFactory extends Factory
{
    use HasSpanishContent;

    protected $model = WorkExperience::class;

    public function definition(): array
    {
        return [
            'candidate_id' => Candidate::factory(),
            'company_name' => $this->faker->company(),
            'job_title' => $this->spanish(self::$spanishJobTitles),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->boolean(70) ? $this->faker->date() : null,
        ];
    }
}
