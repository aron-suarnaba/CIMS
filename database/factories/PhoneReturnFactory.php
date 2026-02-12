<?php

namespace Database\Factories;

use App\Models\PhoneReturn;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhoneReturnFactory extends Factory
{
    protected $model = PhoneReturn::class;

    public function definition(): array
    {
        return [
            'phone_issuance_id' => null, // Will be provided by the Seeder
            'date_returned' => $this->faker->date(),
            'returned_to' => $this->faker->name(),
            'returned_by' => $this->faker->name(),
            'returnee_department' => $this->faker->jobTitle(),
            'returned_accessories' => 'Charger, Case',
        ];
    }
}
