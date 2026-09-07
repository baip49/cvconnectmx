<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\CvAccess;
use Illuminate\Database\Seeder;

class CvAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (CvAccess::query()->exists()) {
            return;
        }

        $companyUserIds = Company::query()->pluck('user_id');

        if ($companyUserIds->isEmpty()) {
            return;
        }

        Candidate::query()->each(function (Candidate $candidate) use ($companyUserIds): void {
            CvAccess::factory()->create([
                'candidate_id' => $candidate->id,
                'accessed_by' => $companyUserIds->random(),
                'application_id' => null,
            ]);
        });
    }
}
