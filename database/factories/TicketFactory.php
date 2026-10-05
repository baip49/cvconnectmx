<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Database\Factories\Concerns\HasSpanishContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    use HasSpanishContent;

    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->spanish(self::$spanishTicketTitles),
            'type' => 'general',
            'level' => $this->faker->randomElement(['low', 'medium', 'high']),
            'status' => 'open',
            'description' => $this->spanish(self::$spanishIncidentDescriptions),
            'created_by' => User::factory(),
            'affected_user_id' => null,
            'claimed_by' => null,
            'secondary_assistant_id' => null,
            'closed_by' => null,
            'closed_at' => null,
            'evidence' => null,
            'detected_at' => now(),
            'lessons_learned' => null,
        ];
    }
}
