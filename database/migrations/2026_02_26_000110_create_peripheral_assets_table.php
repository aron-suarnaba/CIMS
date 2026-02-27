<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('peripheral_assets', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('image_path')->nullable();
            $table->string('model');
            $table->string('serial_num')->unique();
            $table->string('imei_one')->unique()->nullable();
            $table->string('imei_two')->nullable();
            $table->integer('ram');
            $table->integer('rom');
            $table->string('purchase_date')->nullable();
            $table->string('sim_no')->nullable();
            $table->string('status')->default('available');
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peripheral_assets');
    }
};
