<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;

class UserPermissionService
{
    /**
     * Permission codes granted directly to each user according to their role.
     * Administrative permissions (tickets.assign, tickets.manage) are only
     * granted through the admin role and are never assigned to users.
     *
     * @return array<string, list<string>>
     */
    public static function defaultsByRole(): array
    {
        $base = ['tickets.view', 'tickets.create', 'tickets.reply', 'tickets.close', 'tickets.reopen'];

        return [
            'candidate' => $base,
            'company' => [...$base, 'vacancies.manage', 'applications.manage'],
            'support' => [...$base, 'tickets.edit', 'tickets.claim', 'tickets.join', 'candidates.view', 'candidates.manage', 'companies.view', 'companies.manage'],
            'admin' => [],
        ];
    }

    public function grantRoleDefaults(User $user): void
    {
        $roleName = $user->role?->name;

        if (! is_string($roleName)) {
            return;
        }

        $codes = self::defaultsByRole()[$roleName] ?? [];

        if ($codes === []) {
            return;
        }

        $ids = Permission::query()->whereIn('code', $codes)->pluck('id');

        foreach ($ids as $permissionId) {
            $user->permissions()->syncWithoutDetaching([
                $permissionId => ['type' => 'granted', 'granted_by' => null],
            ]);
        }
    }
}
