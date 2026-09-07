<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! AuditLog::query()->exists()) {
            AuditLog::factory(10)->create();
        }
    }
}
