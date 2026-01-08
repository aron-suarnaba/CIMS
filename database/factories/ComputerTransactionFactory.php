<?php

namespace Database\Factories;

use App\Models\ComputerTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComputerTransactionFactory extends Factory
{

    protected $model = ComputerTransaction::class;
    public function definition(): array
    {
        return [
            // host_name will be provided by the Seeder
            'assigned_user' => $this->faker->name(),
            'department' => $this->faker->randomElement(['IT', 'HR', 'Accounting', 'Production', 'Sales']),
            'date_issued' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'pullout_date' => null,
            'pullout_reason' => null,
            'returned_to' => null,
        ];
    }
}
