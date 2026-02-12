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
        Schema::create('phones', function (Blueprint $table) {
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

        // Legacy phone_transactions table for backward compatibility
        // New issuance/return separation is in a separate migration
        if (!Schema::hasTable('phone_transactions')) {
            Schema::create('phone_transactions', function (Blueprint $table) {
                $table->id();
                $table->string('serial_num');
                $table->foreign('serial_num')
                      ->references('serial_num')
                      ->on('phones')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
                $table->string('issued_to');
                $table->string('department');
                $table->date('date_issued');
                $table->string('issued_by');
                $table->text('issued_accessories')->nullable();
                $table->boolean('cashout')->default(false);
                $table->date('date_returned')->nullable();
                $table->string('returned_to')->nullable();
                $table->string('returned_by')->nullable();
                $table->string('returnee_department')->nullable();
                $table->text('returned_accessories')->nullable();
                $table->string('remarks')->nullable();
                $table->timestamps();
            });
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_transactions'); // Drop child first
        Schema::dropIfExists('phones');             // Then parent
    }
};
