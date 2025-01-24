<?php
namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserSeeder
 *
 * Seeder class for creating users in the database. It ensures that an admin user and multiple regular users are created
 * and assigned the appropriate roles. It uses the UserFactory to generate users and assigns the "admin" or "user" role.
 *
 * @package Database\Seeders
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This method creates 1 admin user and 9 regular users with a "user" role.
     * It uses the UserFactory to create the users and assigns them to the "admin" and "user" roles.
     * The admin user has the email "admin@example.com" and the username "admin", while the regular users
     * have randomly generated emails and usernames.
     */
    public function run()
    {
        // Ensure roles exist first (admin and user)
        $adminRole = Role::where('title', 'admin')->first();
        $userRole = Role::where('title', 'user')->first();

        // Create 1 admin user
        User::factory()->create([
            'role_id' => $adminRole->id,  
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'username' => 'admin',
            'password' => Hash::make('admin001'),
        ]);

        User::factory()->create([
            'role_id' => $userRole->id,  
            'name' => 'Admin User',
            'email' => 'adminUser@example.com',
            'username' => 'adminUser',
            'password' => Hash::make('admin001'),
        ]);

        // Create 9 regular user users
        User::factory()->count(8)->create([
            'role_id' => $userRole->id,  
        ]);
    }
}
