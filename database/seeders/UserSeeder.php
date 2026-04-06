<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario admin fijo para iniciar sesión
        User::factory()
            ->admin()
            ->create([
                'name' => 'César Enrique',
                'last_name' => 'Sánchez Montoya',
                'email' => 'cesar@unach.mx',
                'password' => bcrypt('123456789'),
            ]);

        // Crear 5 usuarios tipo Candidato
        User::factory()
            ->count(5)
            ->candidate()
            ->create()
            ->each(function ($user) {
                \App\Models\Candidate::factory()->create([
                    'user_id' => $user->id,
                ]);
            });

        // Crear 5 usuarios tipo Agencia
        User::factory()
            ->count(5)
            ->agency()
            ->create()
            ->each(function ($user) {
                $user->agency()->create();
            });

        // Crear 5 usuarios tipo Contratista
        User::factory()
            ->count(5)
            ->contractor()
            ->create()
            ->each(function ($user) {
                $user->contractor()->create();
            });
    }
}
