<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Application::query()->exists()) {
            return;
        }

        $vacancyIds = Vacancy::query()->pluck('id');

        Candidate::query()->each(function (Candidate $candidate) use ($vacancyIds): void {
            foreach ($vacancyIds->random(min(2, $vacancyIds->count())) as $vacancyId) {
                Application::factory()->create([
                    'candidate_id' => $candidate->id,
                    'vacancy_id' => $vacancyId,
                ]);
            }
        });
    }
}
