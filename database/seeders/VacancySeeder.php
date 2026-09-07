<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;

class VacancySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Vacancy::query()->exists()) {
            return;
        }

        Company::query()->get()->each(function (Company $company): void {
            Vacancy::factory(3)->create(['company_id' => $company->id]);
        });
    }
}
