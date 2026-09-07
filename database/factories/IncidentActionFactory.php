<?php

namespace Database\Factories;

use App\Models\Incident;
use App\Models\IncidentAction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncidentAction>
 */
class IncidentActionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'incident_id' => Incident::factory(),
            'action' => $this->faker->sentence(8),
            'phase' => $this->faker->randomElement(['detection', 'containment', 'recovery']),
            'performed_by' => User::query()->inRandomOrder()->value('id'),
        ];
    }
}
