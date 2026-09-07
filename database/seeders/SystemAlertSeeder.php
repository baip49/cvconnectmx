<?php

namespace Database\Seeders;

use App\Models\SystemAlert;
use Illuminate\Database\Seeder;

class SystemAlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! SystemAlert::query()->exists()) {
            SystemAlert::factory(10)->create();
        }
    }
}
