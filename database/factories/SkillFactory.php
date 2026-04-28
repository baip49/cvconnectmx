<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array
    {
        return [
            'candidate_id' => Candidate::factory(),
            'name' => $this->faker->randomElement(['PHP', 'Laravel', 'Filament', 'JavaScript', 'TailwindCSS', 'MySQL', 'Git', 'Docker']),
            'level' => $this->faker->randomElement(['Principiante', 'Intermedio', 'Avanzado', 'Experto']),
        ];
    }
}
