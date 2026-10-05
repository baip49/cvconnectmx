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
            ['code' => 'candidates.view', 'name' => 'Ver candidatos', 'description' => 'Permite consultar candidatos'],
            ['code' => 'candidates.manage', 'name' => 'Administrar candidatos', 'description' => 'Permite editar candidatos'],
            ['code' => 'tickets.view', 'name' => 'Ver tickets', 'description' => 'Permite consultar tickets de ayuda'],
            ['code' => 'tickets.create', 'name' => 'Crear tickets', 'description' => 'Permite abrir tickets de ayuda'],
            ['code' => 'tickets.reply', 'name' => 'Responder tickets', 'description' => 'Permite responder en tickets de ayuda'],
            ['code' => 'tickets.edit', 'name' => 'Editar tickets', 'description' => 'Permite editar título, tipo y nivel de tickets'],
            ['code' => 'tickets.reopen', 'name' => 'Reabrir tickets', 'description' => 'Permite reabrir tickets cerrados dentro de 30 días'],
            ['code' => 'tickets.claim', 'name' => 'Reclamar tickets', 'description' => 'Permite reclamar tickets sin atender'],
            ['code' => 'tickets.join', 'name' => 'Unirse a tickets', 'description' => 'Permite unirse como segundo asistente'],
            ['code' => 'tickets.assign', 'name' => 'Asignar tickets', 'description' => 'Permite asignar tickets a asistentes'],
            ['code' => 'tickets.close', 'name' => 'Cerrar tickets', 'description' => 'Permite cerrar tickets de ayuda'],
            ['code' => 'tickets.manage', 'name' => 'Administrar tickets', 'description' => 'Permite administrar tickets de ayuda'],
            ['code' => 'roles.manage', 'name' => 'Administrar roles', 'description' => 'Permite administrar roles y sus permisos'],
            ['code' => 'permissions.manage', 'name' => 'Administrar permisos', 'description' => 'Permite administrar permisos del sistema'],
        ];

        foreach ($permissions as $attributes) {
            $permission = Permission::updateOrCreate(['code' => $attributes['code']], $attributes);
            Role::query()->whereIn('name', ['admin'])->first()?->permissions()->syncWithoutDetaching($permission);
        }

        $this->syncRolePermissions();
    }

    /**
     * Default permission sets per role. Admin keeps everything through
     * the sync above; administrative permissions stay exclusive to admin.
     */
    protected function syncRolePermissions(): void
    {
        $matrix = [
            'candidate' => [
                'tickets.view', 'tickets.create', 'tickets.reply', 'tickets.close', 'tickets.reopen',
            ],
            'company' => [
                'tickets.view', 'tickets.create', 'tickets.reply', 'tickets.close', 'tickets.reopen',
                'vacancies.manage', 'applications.manage',
            ],
            'support' => [
                'tickets.view', 'tickets.create', 'tickets.reply', 'tickets.close', 'tickets.reopen',
                'tickets.edit', 'tickets.claim', 'tickets.join',
            ],
        ];

        foreach ($matrix as $roleName => $codes) {
            $role = Role::query()->where('name', $roleName)->first();

            if (! $role) {
                continue;
            }

            $ids = Permission::query()->whereIn('code', $codes)->pluck('id')->all();

            $role->permissions()->syncWithoutDetaching($ids);
        }
    }
}
