<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\CvAccess;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CvAccess>
 */
class CvAccessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'candidate_id' => Candidate::factory(),
            'accessed_by' => User::factory()->company(),
            'application_id' => Application::factory(),
        ];
    }
}
