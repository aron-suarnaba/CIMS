<?php

// Database/Seeders/PhoneSeeder.php

namespace Database\Seeders;

use App\Models\Phone;
use App\Models\PhoneTransaction;
use Illuminate\Database\Seeder;

class PhoneSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create available phones without transactions
        Phone::factory()->count(5)->create([
            'status' => 'available'
        ]);

        // 2. Create issued phones with an active transaction
        Phone::factory()->count(12)->create([
            'status' => 'issued'
        ])->each(function ($phone) {
            PhoneTransaction::factory()->create([
                'serial_num' => $phone->serial_num, // Link via serial_num
                'date_returned' => null,
            ]);
        });

        $returnedPhone = Phone::factory()->create(['status' => 'available']);
        PhoneTransaction::factory()->create([
            'serial_num' => $returnedPhone->serial_num, // Link via serial_num
            'issued_to' => 'John Doe',
            'date_issued' => now()->subYear(),
            'date_returned' => now()->subMonths(2),
            'returned_to' => 'IT Department',
        ]);
    }
}
