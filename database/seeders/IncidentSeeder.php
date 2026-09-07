<?php

namespace Database\Seeders;

use App\Models\Incident;
use Illuminate\Database\Seeder;

class IncidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Incident::query()->exists()) {
            Incident::factory(8)->create();
        }
    }
}
