<?php

namespace Database\Factories;

use App\Models\SystemAlert;
use App\Models\User;
use Database\Factories\Concerns\HasSpanishContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SystemAlert>
 */
class SystemAlertFactory extends Factory
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
            'type' => $this->faker->randomElement(['security', 'maintenance', 'system']),
            'level' => $this->faker->randomElement(['info', 'warning', 'critical']),
            'message' => $this->spanish(self::$spanishAlertMessages),
            'user_id' => User::query()->inRandomOrder()->value('id'),
            'is_resolved' => false,
            'reviewed_by' => null,
        ];
    }
}
