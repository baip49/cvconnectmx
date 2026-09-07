<?php

namespace Database\Seeders;

use App\Models\Training;
use App\Models\User;
use App\Models\UserTraining;
use Illuminate\Database\Seeder;

class UserTrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (UserTraining::query()->exists()) {
            return;
        }

        $trainings = Training::query()->get();

        User::query()->whereHas('role', fn ($query) => $query->whereIn('name', ['admin', 'company']))->get()->each(
            function (User $user) use ($trainings): void {
                $trainings->random(min(2, $trainings->count()))->each(function (Training $training) use ($user): void {
                    UserTraining::factory()->create([
                        'user_id' => $user->id,
                        'training_id' => $training->id,
                    ]);
                });
            }
        );
    }
}
