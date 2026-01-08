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
            [
                'employee_id' => 'SYS001',
                'first_name' => 'System',
                'last_name' => 'Admin',
                'department' => 'IT',
                'position' => 'System Administrator',
                'email' => 'admin@example.com',
                'password' => 'password',
            ],
            [
                'employee_id' => 'PPR997',
                'first_name' => 'Arjay',
                'last_name' => 'Nebres',
                'department' => 'Information Technology',
                'position' => 'IT Supervisor',
                'email' => 'arjay.nebres@printwell.com.ph',
                'password' => 'password',
            ],
            [
                'employee_id' => 'SYS002',
                'first_name' => 'Technical',
                'last_name' => 'Support',
                'department' => 'Information Technology',
                'position' => 'IT Specialist',
                'email' => 'technical.support@printwell.com.ph',
                'password' => 'password',
            ],
            [
                'employee_id' => 'PPR1187',
                'first_name' => 'Aron Kyle',
                'last_name' => 'Suarnaba',
                'department' => 'Information Technology',
                'position' => 'System Analyst Programmer',
                'email' => 'aron.suarnaba@printwell.com.ph',
                'password' => 'password',
            ],
            [
                'employee_id' => 'PPC1190',
                'first_name' => 'Mico',
                'last_name' => 'Limbanganon',
                'department' => 'Information Technology',
                'position' => 'IT Specialist',
                'email' => 'mico.limbanganon@example.com',
                'password' => 'password',
            ],
            [
                'employee_id' => 'PPC1197',
                'first_name' => 'Angelique',
                'last_name' => 'Limbanganon',
                'department' => 'Information Technology',
                'position' => 'IT Specialist',
                'email' => 'angelique.limbanganon.printwell.com.ph',
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
