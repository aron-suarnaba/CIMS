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
            $table->string('model');
            $table->string('serial_num')->unique();
            $table->string('imei_one')->unique();
            $table->string('imei_two')->nullable();
            $table->string('ram');
            $table->string('rom');
            $table->string('purchase_date')->nullable();
            $table->string('status')->default('available'); // available, issued, maintenance
            $table->timestamps();
        });

        Schema::create('phone_transactions', function (Blueprint $table) {
            $table->id();

            $table->string('serial_num');
            // Issuance Info
            $table->foreign('serial_num')
                  ->references('serial_num')
                  ->on('phones')
                  ->cascade('cascade')
                  ->onUpdate('cascade');
            $table->string('issued_to');
            $table->string('department');
            $table->date('date_issued');
            $table->string('issued_by');
            $table->text('issued_accessories')->nullable();
            $table->boolean('it_ack_issued')->default(false);
            $table->boolean('purch_ack_issued')->default(false);

            // Return Info (Starts as Null)
            $table->date('date_returned')->nullable();
            $table->string('returned_to')->nullable();
            $table->text('returned_accessories')->nullable();
            $table->boolean('it_ack_returned')->default(false);
            $table->boolean('purch_ack_returned')->default(false);

            $table->string('remarks')->nullable();
            $table->timestamps();
        });
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
