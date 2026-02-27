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
        Schema::create('networking_issuances', function (Blueprint $table) {
            $table->id();
            $table->string('serial_num');
            $table->foreign('serial_num')
                ->references('serial_num')
                ->on('networking_assets')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('issued_to');
            $table->string('department');
            $table->date('date_issued');
            $table->string('issued_by');
            $table->text('issued_accessories')->nullable();
            $table->boolean('charger')->default(false);
            $table->boolean('headphones')->default(false);
            $table->boolean('acknowledgement')->default(false);
            $table->boolean('cashout')->default(false);
            $table->string('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('networking_returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('networking_issuance_id');
            $table->foreign('networking_issuance_id')
                ->references('id')
                ->on('networking_issuances')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->date('date_returned');
            $table->string('returned_to');
            $table->string('returned_by');
            $table->string('returnee_department');
            $table->text('returned_accessories')->nullable();
            $table->boolean('charger')->default(false);
            $table->boolean('headphones')->default(false);
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('networking_returns');
        Schema::dropIfExists('networking_issuances');
    }
};
