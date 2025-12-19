<?php

namespace Database\Seeders;

use App\Models\Phone;
use App\Models\PhoneTransaction;
use Illuminate\Database\Seeder;

class PhoneSeeder extends Seeder
{
    public function run(): void
    {
        Phone::factory()->count(10)->create([
            'status' => 'available'
        ]);

        Phone::factory()->count(10)->create([
            'status' => 'issued'
        ])->each(function ($phone) {
            PhoneTransaction::factory()->create([
                'phone_id' => $phone->id,
                'date_returned' => null,
            ]);
        });

        $returnedPhone = Phone::factory()->create(['status' => 'available']);
        PhoneTransaction::factory()->create([
            'phone_id' => $returnedPhone->id,
            'issued_to' => 'John Doe',
            'date_issued' => now()->subYear(),
            'date_returned' => now()->subMonths(2),
            'returned_to' => 'IT Department',
            'it_ack_returned' => true,
            'remarks' => 'Unit returned in good condition.'
        ]);
    }
}
