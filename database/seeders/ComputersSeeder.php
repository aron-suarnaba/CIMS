<?php

namespace Database\Seeders;

use App\Models\Computers;
use App\Models\ComputerTransaction;
use Illuminate\Database\Seeder;

class ComputersSeeder extends Seeder
{
    public function run(): void
    {
        // Create 20 computers
        Computers::factory(8)->create()->each(function ($computer) {

            ComputerTransaction::factory()->create([
                'host_name' => $computer->host_name,
                'assigned_user' => ($computer->status === 'In Use') ? $computer->remarks : 'Admin',
            ]);
        });
    }
}
