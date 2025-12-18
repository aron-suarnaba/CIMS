<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PhoneSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('phone')->insert([
            [
                'model' => 'iPhone 15 Pro',
                'serial_num' => 'SN-AAPL-9921',
                'brand' => 'Apple',
                'ram' => '8GB',
                'rom' => '256GB',
                'imei_one' => '354211000123456',
                'issued_to' => 'John Doe',
                'date_issued' => Carbon::now()->subMonths(2),
                'issuedAcknowledgementIT' => true,
                'created_at' => Carbon::now(),
            ],
            [
                'model' => 'Galaxy S23 Ultra',
                'serial_num' => 'SN-SAMS-4432',
                'brand' => 'Samsung',
                'ram' => '12GB',
                'rom' => '512GB',
                'imei_one' => '358822000654321',
                'issued_to' => 'Jane Smith',
                'date_issued' => Carbon::now()->subDays(15),
                'issuedAcknowledgementIT' => true,
                'created_at' => Carbon::now(),
            ],
            [
                'model' => 'Redmi Note 12',
                'serial_num' => 'SN-REDM-1102',
                'brand' => 'Redmi',
                'ram' => '6GB',
                'rom' => '128GB',
                'imei_one' => '864433000987654',
                'issued_to' => 'Alice Johnson',
                'date_issued' => Carbon::now()->subMonths(1),
                'issuedAcknowledgementIT' => false,
                'created_at' => Carbon::now(),
            ],
            [
                'model' => 'Oppo Reno 10',
                'serial_num' => 'SN-OPPO-8877',
                'brand' => 'Oppo',
                'ram' => '8GB',
                'rom' => '256GB',
                'imei_one' => '352211444555666',
                'issued_to' => 'Bob Wilson',
                'date_issued' => null, // Not issued yet
                'issuedAcknowledgementIT' => false,
                'created_at' => Carbon::now(),
            ],
            [
                'model' => 'Vivo V27',
                'serial_num' => 'SN-VIVO-3344',
                'brand' => 'Vivo',
                'ram' => '8GB',
                'rom' => '128GB',
                'imei_one' => '359988111222333',
                'issued_to' => 'Charlie Davis',
                'date_issued' => Carbon::now()->subDays(5),
                'issuedAcknowledgementIT' => true,
                'created_at' => Carbon::now(),
            ],
        ]);
    }
}
