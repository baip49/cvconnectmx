<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\User;
use Database\Factories\Concerns\HasSpanishContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidateFactory extends Factory
{
    use HasSpanishContent;

    protected $model = Candidate::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'professional_title' => $this->spanish(self::$spanishJobTitles),
            'summary' => 'Profesionista con experiencia comprobable en busca de nuevas oportunidades laborales en Chiapas.',
            'city' => $this->spanish(self::$spanishCities),
            'expected_salary' => $this->faker->randomFloat(2, 10000, 50000),
            'phone_encrypted' => $this->faker->phoneNumber(),
            'is_public_profile' => true,
        ];
    }
}
