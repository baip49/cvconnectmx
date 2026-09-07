<?php

namespace Database\Seeders;

use App\Models\Incident;
use App\Models\IncidentAction;
use Illuminate\Database\Seeder;

class IncidentActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (IncidentAction::query()->exists()) {
            return;
        }

        Incident::query()->each(function (Incident $incident): void {
            IncidentAction::factory(2)->create(['incident_id' => $incident->id]);
        });
    }
}
