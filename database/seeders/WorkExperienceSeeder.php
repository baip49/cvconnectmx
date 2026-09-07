<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\WorkExperience;
use Illuminate\Database\Seeder;

class WorkExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (WorkExperience::query()->exists()) {
            return;
        }

        Candidate::query()->each(fn (Candidate $candidate) => WorkExperience::factory(2)->create([
            'candidate_id' => $candidate->id,
        ]));
    }
}
