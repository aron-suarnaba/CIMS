<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('phone_returns', function (Blueprint $table) {
            $table->boolean('charger')->default(false)->after('returned_accessories');
            $table->boolean('headphones')->default(false)->after('charger');
        });

        // Backfill from legacy free-text returned_accessories values.
        DB::table('phone_returns')
            ->select('id', 'returned_accessories')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    $raw = strtolower((string) ($row->returned_accessories ?? ''));

                    DB::table('phone_returns')
                        ->where('id', $row->id)
                        ->update([
                            'charger' => str_contains($raw, 'charger'),
                            'headphones' => str_contains($raw, 'headphone') || str_contains($raw, 'earphone'),
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phone_returns', function (Blueprint $table) {
            $table->dropColumn(['charger', 'headphones']);
        });
    }
};
