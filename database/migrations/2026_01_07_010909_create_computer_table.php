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
        Schema::create('computers', function (Blueprint $table) {
            $table->id();
            $table->string('host_name')->unique();
            $table->string('serial_number')->unique()->nullable();
            $table->string('manufacturer');
            $table->string('model');

            // Technical Specs
            $table->string('os_version')->nullable()->default('windows');
            $table->string('cpu')->nullable();
            $table->integer('ram_gb')->nullable();
            $table->integer('storage_gb')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('ip_address')->nullable();

            // Financial
            $table->date('purchase_date')->nullable();
            $table->string('vendor')->nullable();
            $table->string('po_number')->nullable();
            $table->date('warranty_expiry')->nullable();

            // Current State Only
            $table->enum('status', ['In Use', 'In Storage', 'In Repair', 'Retired', 'Broken'])->default('In Storage');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('computer_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('host_name');
            $table->foreign('host_name')
            ->references('host_name')
            ->on('computers')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            // Assignment (Issuance)
            $table->string('assigned_user');
            $table->string('department');
            $table->date('date_issued');

            // Pullout (Return)
            $table->date('pullout_date')->nullable();
            $table->string('pullout_reason')->nullable();
            $table->string('returned_to')->nullable();
            $table->string('date_pullout')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('computer_transactions');
        Schema::dropIfExists('computers');
    }
};
