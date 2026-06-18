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
        Schema::table('maintenance_requests', function (Blueprint $table) {
            // Add customer_id column after id or after commercial_route_id
            $table->unsignedBigInteger('customer_id')->nullable()->after('commercial_route_id');

            // Add foreign key constraint (if users table exists)
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['customer_id']);

            // Then drop the column
            $table->dropColumn('customer_id');
        });
    }
};
