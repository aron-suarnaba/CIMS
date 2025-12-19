<?php

namespace Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;

class PhoneFactory extends Factory
{
    public function definition(): array
    {
        $brands = ['Apple', 'Samsung', 'Oppo', 'Vivo', 'Redmi', 'Xiaomi', 'Honor', 'Realme', 'Techno', 'Others'];
        $brand = $this->faker->randomElement($brands);

        return [
            'brand' => $brand,
            'model' => $brand . ' ' . $this->faker->word(),
            'serial_num' => strtoupper($this->faker->bothify('SN-####-????')),
            'imei_one' => $this->faker->numerify('###############'), // 15 digits
            'imei_two' => $this->faker->numerify('###############'),
            'ram' => $this->faker->randomElement(['3GB', '4GB', '8GB', '12GB', '16GB', '24GB']),
            'rom' => $this->faker->randomElement([ '16GB', '32GB', '64GB', '128GB', '256GB']),
            'purchase_date' => $this->faker->date(),
            'status' => 'available',
        ];
    }
}
