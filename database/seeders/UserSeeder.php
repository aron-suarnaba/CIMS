<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define key roles for easy reference
        $usersToSeed = [
            // 1. System Administrator (IT)
            [
                'employee_id' => 'SYS001', // <-- ADDED
                'first_name' => 'System',
                'last_name' => 'Admin',
                'department' => 'IT',
                'position' => 'System Administrator',
                'email' => 'admin@example.com',
                'password' => 'password',
            ],
            // 2. IT User
            [
                'employee_id' => 'IT105', // <-- ADDED
                'first_name' => 'Alice',
                'last_name' => 'Smith',
                'department' => 'IT',
                'position' => 'IT Support Specialist',
                'email' => 'alice.smith@example.com',
                'password' => 'password',
            ],
            // 3. Purchasing User
            [
                'employee_id' => 'PUR201', // <-- ADDED
                'first_name' => 'Carol',
                'last_name' => 'White',
                'department' => 'Purchasing',
                'position' => 'Procurement Officer',
                'email' => 'carol.white@example.com',
                'password' => 'password',
            ],
            // 4. Supervisor (New Role)
            [
                'employee_id' => 'OPS304', // <-- ADDED
                'first_name' => 'John',
                'last_name' => 'Doe',
                'department' => 'Information Technology',
                'position' => 'IT Supervisor',
                'email' => 'john.doe@example.com',
                'password' => 'password',
            ],
        ];

        foreach ($usersToSeed as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'employee_id' => $userData['employee_id'], // <-- Mapped the new field
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'department' => $userData['department'],
                    'position' => $userData['position'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Optional: Create 5 fake users using the User Factory (for local environments)
        if (app()->environment('local') && method_exists(User::class, 'factory')) {
            // NOTE: The UserFactory MUST also be updated to generate a fake employee_id.
            User::factory(5)->create();
        }
    }
}
