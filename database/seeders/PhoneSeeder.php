<?php

// Database/Seeders/PhoneSeeder.php

namespace Database\Seeders;

use App\Models\Phone;
use App\Models\PhoneIssuance;
use App\Models\PhoneReturn;
use Illuminate\Database\Seeder;

class PhoneSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create available phones without transactions
        Phone::factory()->count(5)->create([
            'status' => 'available'
        ]);

        // 2. Create issued phones with an active issuance (no return)
        Phone::factory()->count(12)->create([
            'status' => 'issued'
        ])->each(function ($phone) {
            PhoneIssuance::factory()->create([
                'serial_num' => $phone->serial_num,
            ]);
        });

        // 3. Create a phone with issuance and return
        $returnedPhone = Phone::factory()->create(['status' => 'available']);
        $issuance = PhoneIssuance::factory()->create([
            'serial_num' => $returnedPhone->serial_num,
            'issued_to' => 'John Doe',
            'date_issued' => now()->subYear(),
        ]);

        PhoneReturn::factory()->create([
            'phone_issuance_id' => $issuance->id,
            'date_returned' => now()->subMonths(2),
            'returned_to' => 'IT Department',
        ]);
    }
}
