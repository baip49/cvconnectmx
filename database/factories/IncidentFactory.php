<?php

namespace Database\Factories;

use App\Models\Incident;
use App\Models\User;
use Database\Factories\Concerns\HasSpanishContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Incident>
 */
class IncidentFactory extends Factory
{
    use HasSpanishContent;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['failed_login', 'suspicious_activity', 'data_access']),
            'level' => $this->faker->randomElement(['low', 'medium', 'high']),
            'status' => $this->faker->randomElement(['open', 'in_progress', 'resolved', 'closed']),
            'description' => $this->spanish(self::$spanishIncidentDescriptions),
            'affected_user_id' => User::query()->inRandomOrder()->value('id'),
            'evidence' => ['source' => 'seed'],
            'detected_at' => now()->subDays($this->faker->numberBetween(0, 30)),
            'lessons_learned' => $this->faker->optional()->randomElement(self::$spanishActionTexts),
        ];
    }
}
