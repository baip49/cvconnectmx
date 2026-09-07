<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Skill::query()->exists()) {
            return;
        }

        Candidate::query()->each(fn (Candidate $candidate) => Skill::factory(5)->create([
            'candidate_id' => $candidate->id,
        ]));
    }
}
