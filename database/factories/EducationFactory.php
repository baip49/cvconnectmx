<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Education;
use Database\Factories\Concerns\HasSpanishContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducationFactory extends Factory
{
    use HasSpanishContent;

    protected $model = Education::class;

    public function definition(): array
    {
        return [
            'candidate_id' => Candidate::factory(),
            'institution' => $this->spanish(self::$spanishInstitutions),
            'degree' => $this->faker->randomElement(['Licenciatura', 'Maestría', 'Doctorado', 'Diplomado']),
        ];
    }
}
