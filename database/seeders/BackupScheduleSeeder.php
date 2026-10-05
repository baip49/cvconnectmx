<?php

namespace Database\Seeders;

use App\Models\BackupSchedule;
use Illuminate\Database\Seeder;

class BackupScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = [
            [
                'name' => 'Respaldo diario de base de datos',
                'scope' => 'database',
                'frequency' => 'daily',
                'time' => '02:00',
                'retention_days' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Respaldo mensual de archivos',
                'scope' => 'files',
                'frequency' => 'monthly',
                'time' => '03:00',
                'retention_days' => 90,
                'is_active' => true,
            ],
        ];

        foreach ($schedules as $attributes) {
            BackupSchedule::firstOrCreate(['name' => $attributes['name']], $attributes);
        }
    }
}
