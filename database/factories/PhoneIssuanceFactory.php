<?php

namespace Database\Factories;

use App\Models\PhoneIssuance;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhoneIssuanceFactory extends Factory
{
    protected $model = PhoneIssuance::class;

    public function definition(): array
    {
        return [
            'serial_num' => null, // Will be provided by the Seeder
            'issued_to' => $this->faker->name(),
            'department' => $this->faker->jobTitle(),
            'date_issued' => $this->faker->date(),
            'issued_by' => 'IT Admin',
            'issued_accessories' => 'Charger, Case',
            'cashout' => true,
        ];
    }
}
