<?php

namespace Database\Factories;

use App\Models\PhoneTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhoneTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'issued_to' => $this->faker->name(),
            'department' => $this->faker->randomElement(['IT', 'Marketing', 'HR', 'Planning', 'Production', 'Accounting', 'Purchasing', 'Reception', 'TSQA', 'Prepress']),
            'date_issued' => $this->faker->date(),
            'issued_by' => 'Admin User',
            'issued_accessories' => 'Charger, Case, Screen Protector',
            'it_ack_issued' => true,
            'purch_ack_issued' => true,
            'remarks' => 'Brand new unit issued.',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
