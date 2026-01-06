<?php

namespace Database\Factories;

use App\Models\PhoneTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhoneTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'serial_num' => null, // Will be provided by the Seeder
            'issued_to' => $this->faker->name(),
            'department' => $this->faker->jobTitle(),
            'date_issued' => $this->faker->date(),
            'issued_by' => 'IT Admin',
            'issued_accessories' => 'Charger, Case',
            'it_ack_issued' => true,
            'purch_ack_issued' => true,
            'date_returned' => $this->faker->date(),
            'returned_by' => $this->faker->name(),
            'returnee_department' => $this->faker->jobTitle(),
            'remarks' => 'Brand new unit issued.',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
