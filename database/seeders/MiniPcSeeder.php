<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MiniPc;

class MiniPcSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // generate 20 sample mini PCs
        MiniPc::factory()->count(20)->create();
    }
}
