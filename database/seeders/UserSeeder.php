<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

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
                'email' => 'cesar@unach.mx',
                'password' => bcrypt('123456789'),
                'name' => 'César Enrique',
                'last_name' => 'Sánchez Montoya',
            ]);
    }
}
