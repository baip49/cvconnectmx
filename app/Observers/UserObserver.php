<?php

namespace App\Observers;

use App\Models\User;
use App\Services\UserPermissionService;

class UserObserver
{
    public function __construct(protected UserPermissionService $permissions) {}

    public function created(User $user): void
    {
        $this->permissions->grantRoleDefaults($user->fresh('role'));
    }
}
