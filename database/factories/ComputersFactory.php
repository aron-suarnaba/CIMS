<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ComputersFactory extends Factory
{
    public function definition(): array
    {
        $manufacturers = ['Dell', 'HP', 'Lenovo', 'Apple', 'ASUS'];
        $manufacturer = $this->faker->randomElement($manufacturers);

        return [
            'host_name' => 'PC-' . $this->faker->unique()->bothify('??###'),
            'serial_number' => $this->faker->unique()->regexify('[A-Z0-9]{10}'),
            'manufacturer' => $manufacturer,
            'model' => $this->faker->word() . ' Pro',
            'os_version' => $this->faker->randomElement(['Windows 10', 'Windows 11', 'macOS Sequoia']),
            'cpu' => $this->faker->randomElement(['Intel i5-12400', 'Intel i7-13700', 'AMD Ryzen 5 5600X']),
            'ram_gb' => $this->faker->randomElement([8, 16, 32, 64]),
            'storage_gb' => $this->faker->randomElement([256, 512, 1024]),
            'mac_address' => $this->faker->macAddress(),
            'ip_address' => $this->faker->ipv4(),
            'purchase_date' => $this->faker->date(),
            'vendor' => $this->faker->company(),
            'po_number' => 'PO-' . $this->faker->numerify('#####'),
            'warranty_expiry' => $this->faker->dateTimeBetween('now', '+3 years'),
            'status' => $this->faker->randomElement(['In Use', 'In Storage', 'In Repair', 'Retired']),
            'remarks' => $this->faker->sentence(),
        ];
    }
}
