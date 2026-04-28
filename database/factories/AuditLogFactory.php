<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => $this->faker->randomElement(['login', 'logout', 'create_user', 'update_profile', 'delete_vacancy']),
            'entity_type' => $this->faker->randomElement(['User', 'Vacancy', 'Candidate', 'Company']),
            'entity_id' => $this->faker->randomNumber(2),
            'old_data' => null,
            'new_data' => ['status' => 'updated'],
            'ip_address' => $this->faker->ipv4(),
            'result' => 'success',
        ];
    }
}
