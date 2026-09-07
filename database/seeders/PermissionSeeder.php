<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['code' => 'users.view', 'name' => 'Ver usuarios', 'description' => 'Permite consultar usuarios'],
            ['code' => 'users.manage', 'name' => 'Administrar usuarios', 'description' => 'Permite crear, editar y eliminar usuarios'],
            ['code' => 'vacancies.manage', 'name' => 'Administrar vacantes', 'description' => 'Permite administrar vacantes'],
            ['code' => 'applications.manage', 'name' => 'Administrar postulaciones', 'description' => 'Permite administrar postulaciones'],
            ['code' => 'audit.view', 'name' => 'Ver auditoría', 'description' => 'Permite consultar registros de auditoría'],
        ];

        foreach ($permissions as $attributes) {
            $permission = Permission::updateOrCreate(['code' => $attributes['code']], $attributes);
            Role::query()->whereIn('name', ['admin'])->first()?->permissions()->syncWithoutDetaching($permission);
        }
    }
}
