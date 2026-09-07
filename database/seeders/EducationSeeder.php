<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Education;
use Illuminate\Database\Seeder;

class EducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Education::query()->exists()) {
            return;
        }

        Candidate::query()->each(fn (Candidate $candidate) => Education::factory()->create([
            'candidate_id' => $candidate->id,
        ]));
    }
}
