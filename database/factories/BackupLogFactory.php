<?php

namespace Database\Factories;

use App\Models\BackupLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BackupLog>
 */
class BackupLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'database',
            'frequency' => $this->faker->randomElement(['daily', 'weekly', 'monthly']),
            'destination_path' => 'backups/'.now()->format('Y/m/d').'.sql.gz',
            'size_bytes' => $this->faker->numberBetween(100000, 5000000),
            'checksum_sha256' => hash('sha256', $this->faker->uuid()),
            'is_encrypted' => true,
            'status' => $this->faker->randomElement(['completed', 'failed']),
            'restoration_tested' => $this->faker->boolean(70),
            'retention_days' => $this->faker->randomElement([7, 30, 90]),
            'executed_by' => User::query()->inRandomOrder()->value('id'),
        ];
    }
}
