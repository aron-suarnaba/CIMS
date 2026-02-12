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
            'model' => $this->faker->word(),
            'serial_num' => strtoupper($this->faker->bothify('SN-####-????')),
            'imei_one' => $this->faker->numerify('###############'), // 15 digits
            'imei_two' => $this->faker->numerify('###############'),
            'ram' => $this->faker->randomElement([3, 4, 8, 12, 16, 24]),
            'rom' => $this->faker->randomElement([16, 32, 64, 128, 256, 512]),
            'sim_no' => $this->faker->numerify('###########'),
            'purchase_date' => $this->faker->date(),
            'status' => 'available',
        ];
    }
}
