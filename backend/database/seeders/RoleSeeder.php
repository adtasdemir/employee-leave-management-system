<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Class RoleSeeder
 *
 * Seeder class for creating roles in the database. It creates two roles: one for administrators with full access
 * and one for regular users with limited access. These roles are used to control access permissions for users in the system.
 *
 * @package Database\Seeders
 */
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This method creates two roles:
     * 1. The "admin" role with full access to the system.
     * 2. The "user" role with limited access, meant for regular users.
     *
     * Both roles are created with the necessary titles and descriptions to define their purpose.
     *
     * @return void
     */
    public function run(): void
    {
        // Create one admin role
        Role::factory()->create([
            'title' => 'admin',
            'description' => 'Administrator role with full access.'
        ]);

        // Create one user role
        Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
    }
}
