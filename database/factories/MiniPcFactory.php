<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MiniPcFactory extends Factory
{
    public function definition(): array
    {
        return [
            'manufacturer_model' => $this->faker->company() . ' ' . $this->faker->word(),
            'cpu' => $this->faker->randomElement(['Intel i5', 'Intel i7', 'AMD Ryzen 5', 'AMD Ryzen 7', 'Intel i3']),
            'ram' => $this->faker->randomElement(['4GB', '8GB', '16GB', '32GB']),
            'rom' => $this->faker->randomElement(['128GB', '256GB', '512GB', '1TB']),
            'mac' => strtoupper($this->faker->macAddress()),
            'ip' => $this->faker->ipv4(),
            'name' => $this->faker->word(),
            'purchase_date' => $this->faker->date(),
            'warranty_expiry' => $this->faker->dateTimeBetween('now', '+3 years')->format('Y-m-d'),
            'remarks' => $this->faker->sentence(),
            'status' => 'available',
        ];
    }
}
