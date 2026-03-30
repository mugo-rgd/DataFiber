<?php
// database/migrations/2024_01_15_000003_add_status_to_users_table.php

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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->string('phone')->nullable();
            // $table->text('address')->nullable();

            // Add indexes for better performance
            $table->index(['status', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status',  'address']);
        });
    }
};
