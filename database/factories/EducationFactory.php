<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Education;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducationFactory extends Factory
{
    protected $model = Education::class;

    public function definition(): array
    {
        return [
            'candidate_id' => Candidate::factory(),
            'institution' => $this->faker->company().' University',
            'degree' => $this->faker->randomElement(['Licenciatura', 'Maestría', 'Doctorado', 'Diplomado']),
        ];
    }
}
