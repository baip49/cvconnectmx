<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\WorkExperience;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkExperienceFactory extends Factory
{
    protected $model = WorkExperience::class;

    public function definition(): array
    {
        return [
            'candidate_id' => Candidate::factory(),
            'company_name' => $this->faker->company(),
            'job_title' => $this->faker->jobTitle(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->boolean(70) ? $this->faker->date() : null,
        ];
    }
}
