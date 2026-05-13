<?php
// In database/migrations/xxxx_xx_xx_xxxxxx_fix_lease_billings_status_column.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Option 1: Change to string with sufficient length
        Schema::table('lease_billings', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->change();
        });

        // Or Option 2: If it's an ENUM, make sure it includes all possible statuses
        // DB::statement("ALTER TABLE lease_billings MODIFY status ENUM('pending', 'paid', 'overdue', 'cancelled') DEFAULT 'pending'");
    }

    public function down()
    {
        // Revert if needed
        Schema::table('lease_billings', function (Blueprint $table) {
            $table->string('status', 10)->default('pending')->change();
        });
    }
};
