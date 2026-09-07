<?php

namespace Database\Seeders;

use App\Models\LoginAttempt;
use Illuminate\Database\Seeder;

class LoginAttemptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! LoginAttempt::query()->exists()) {
            LoginAttempt::factory(12)->create();
        }
    }
}
