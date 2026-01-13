<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('network_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('ip_address')->unique();
            $table->string('community')->nullable()->default('public');
            $table->string('status')->default('offline');
            $table->string('uptime')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('network_devices');
    }
};
