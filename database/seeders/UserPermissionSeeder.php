<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->whereHas('role', fn ($query) => $query->where('name', 'admin'))->first();

        if (! $admin) {
            return;
        }

        Permission::query()->each(function (Permission $permission) use ($admin): void {
            DB::table('user_permissions')->updateOrInsert(
                [
                    'user_id' => $admin->id,
                    'permission_id' => $permission->id,
                ],
                [
                    'type' => 'granted',
                    'granted_by' => $admin->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        });
    }
}
