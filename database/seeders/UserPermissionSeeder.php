<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\UserPermissionService;
use Illuminate\Database\Seeder;

class UserPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(UserPermissionService::class);

        User::query()->with('role')->each(function (User $user) use ($service): void {
            $service->grantRoleDefaults($user);
        });
    }
}
