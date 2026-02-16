<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create phone_issuances table
        Schema::create('phone_issuances', function (Blueprint $table) {
            $table->id();
            $table->string('serial_num');

            // Foreign key to phones table
            $table->foreign('serial_num')
                ->references('serial_num')
                ->on('phones')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Issuance Info
            $table->string('issued_to');
            $table->string('department');
            $table->date('date_issued');
            $table->string('issued_by');
            $table->text('issued_accessories')->nullable();
            $table->boolean('charger')->default(false);
            $table->boolean('headphones')->default(false);
            $table->boolean('acknowledgement')->default(false);
            $table->boolean('cashout')->default(false);

            $table->timestamps();
        });

        // Create phone_returns table
        Schema::create('phone_returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('phone_issuance_id');

            // Foreign key to phone_issuances table
            $table->foreign('phone_issuance_id')
                ->references('id')
                ->on('phone_issuances')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Return Info
            $table->date('date_returned');
            $table->string('returned_to');
            $table->string('returned_by');
            $table->string('returnee_department');
            $table->text('returned_accessories')->nullable();

            $table->timestamps();
        });

        // Migrate data from phone_transactions to new tables if it exists
        if (Schema::hasTable('phone_transactions')) {
            $transactions = DB::table('phone_transactions')->get();

            foreach ($transactions as $transaction) {
                // Insert into phone_issuances
                $issuanceId = DB::table('phone_issuances')->insertGetId([
                    'serial_num' => $transaction->serial_num,
                    'issued_to' => $transaction->issued_to,
                    'department' => $transaction->department,
                    'date_issued' => $transaction->date_issued,
                    'issued_by' => $transaction->issued_by,
                    'issued_accessories' => $transaction->issued_accessories,
                    'charger' => $transaction->charger,
                    'headphones' => $transaction->headphones,
                    'cashout' => $transaction->cashout,
                    'acknowledgement' => $transaction->acknowledgement,
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at,
                ]);

                // Insert into phone_returns only if date_returned is not null
                if ($transaction->date_returned !== null) {
                    DB::table('phone_returns')->insert([
                        'phone_issuance_id' => $issuanceId,
                        'date_returned' => $transaction->date_returned,
                        'returned_to' => $transaction->returned_to,
                        'returned_by' => $transaction->returned_by,
                        'returnee_department' => $transaction->returnee_department,
                        'returned_accessories' => $transaction->returned_accessories,
                        'created_at' => $transaction->created_at,
                        'updated_at' => $transaction->updated_at,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_returns');
        Schema::dropIfExists('phone_issuances');
    }
};
