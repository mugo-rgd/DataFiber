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
        Schema::table('quotations', function (Blueprint $table) {
            // Add customer_id column as foreign key
            $table->foreignId('customer_id')
                  ->after('design_request_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Optional: Add index for better performance
            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['customer_id']);
            // Then drop the column
            $table->dropColumn('customer_id');
        });
    }
};
