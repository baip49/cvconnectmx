<?php

namespace Database\Factories;

use App\Models\Incident;
use App\Models\IncidentAction;
use App\Models\User;
use Database\Factories\Concerns\HasSpanishContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncidentAction>
 */
class IncidentActionFactory extends Factory
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
            'incident_id' => Incident::factory(),
            'action' => $this->spanish(self::$spanishActionTexts),
            'phase' => $this->faker->randomElement(['detection', 'containment', 'recovery']),
            'performed_by' => User::query()->inRandomOrder()->value('id'),
        ];
    }
}
