<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Check if payments table exists
        if (Schema::hasTable('payments')) {

            // Add lease_id column if it doesn't exist
            if (!Schema::hasColumn('payments', 'lease_id')) {
                Schema::table('payments', function (Blueprint $table) {
                    // Add the lease_id column as foreign key
                    $table->foreignId('lease_id')
                          ->nullable() // Make it nullable initially
                          ->constrained('leases')
                          ->onDelete('cascade');
                });
            }

            // If you want to make it required after data migration, you can remove nullable later
        } else {
            // If payments table doesn't exist, create it
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lease_id')->constrained()->onDelete('cascade');
                $table->decimal('amount', 10, 2);
                $table->date('payment_date');
                $table->string('status')->default('pending');
                $table->string('reference')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index('lease_id');
                $table->index('payment_date');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'lease_id')) {
            Schema::table('payments', function (Blueprint $table) {
                // Drop foreign key first
                $table->dropForeign(['lease_id']);
                // Then drop the column
                $table->dropColumn('lease_id');
            });
        }
    }
};
