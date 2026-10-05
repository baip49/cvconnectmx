<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Rol de administrador con acceso total',
                'active' => true,
            ],
            [
                'name' => 'candidate',
                'description' => 'Rol de candidato para buscar empleo',
                'active' => true,
            ],
            [
                'name' => 'company',
                'description' => 'Rol de empresa para contratar personal',
                'active' => true,
            ],
            [
                'name' => 'support',
                'description' => 'Rol de asistencia para atender tickets de ayuda',
                'active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
