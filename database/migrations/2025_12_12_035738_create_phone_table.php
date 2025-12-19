<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('phone', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('model');
            $table->string('serial_num');
            $table->string('brand');
            $table->string('ram');
            $table->string('rom')->nullable();
            $table->string('imei_one')->nullable();
            $table->string('imei_two')->nullable();
            $table->string('remarks')->nullable();
            $table->string('status')->nullable();
            $table->string('issued_accessories')->nullable();
            $table->boolean('with_cashout')->nullable();
            $table->string('issued_to')->nullable();
            $table->string('issued_to_department')->nullable();
            $table->date('date_issued')->nullable();
            $table->string('issued_by')->nullable();
            $table->boolean('issuedAcknowledgementIT')->nullable();
            $table->boolean('issuedAcknowledgementPurchasing')->nullable();
            $table->string('returned_accessories')->nullable();
            $table->string('returned_by')->nullable();
            $table->date('returned_date')->nullable();
            $table->string('returned_to')->nullable();
            $table->boolean('returnedAcknowledgementIT')->nullable();
            $table->boolean('returnedAcknowledgementPurchasing')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone');
    }
};
