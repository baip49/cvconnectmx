<?php

namespace Database\Seeders;

use App\Models\BackupLog;
use Illuminate\Database\Seeder;

class BackupLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! BackupLog::query()->exists()) {
            BackupLog::factory(5)->create();
        }
    }
}
