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
        Schema::create('mini_pcs', function (Blueprint $table) {
            $table->id();
            $table->string('manufacturer_model');
            $table->string('cpu')->nullable();
            $table->string('ram')->nullable();
            $table->string('rom')->nullable();
            $table->string('mac')->nullable();
            $table->string('ip')->nullable();
            $table->string('name')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->text('remarks')->nullable();
            $table->string('status')->default('available');
            $table->timestamps();
        });

        Schema::create('mini_pc_issuances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mini_pc_id');
            $table->foreign('mini_pc_id')
                ->references('id')
                ->on('mini_pcs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('department');
            $table->string('location');
            $table->date('date_issued');
            $table->timestamps();
        });

        Schema::create('mini_pc_pullouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mini_pc_issuance_id');
            $table->foreign('mini_pc_issuance_id')
                ->references('id')
                ->on('mini_pc_issuances')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->date('pullout_date');
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mini_pc_pullouts');
        Schema::dropIfExists('mini_pc_issuances');
        Schema::dropIfExists('mini_pcs');
    }
};
