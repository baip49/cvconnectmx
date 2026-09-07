<?php

namespace Database\Seeders;

use App\Models\PasswordHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class PasswordHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (PasswordHistory::query()->exists()) {
            return;
        }

        User::query()->each(function (User $user): void {
            PasswordHistory::factory(2)->create(['user_id' => $user->id]);
        });
    }
}
