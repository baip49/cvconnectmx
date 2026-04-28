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
                'description' => 'Administrator role with full access',
                'active' => true,
            ],
            [
                'name' => 'candidate',
                'description' => 'Candidate role for job seekers',
                'active' => true,
            ],
            [
                'name' => 'company',
                'description' => 'Company role for employers',
                'active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
