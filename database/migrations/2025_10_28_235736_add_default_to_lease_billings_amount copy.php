<?php
// In database/migrations/xxxx_xx_xx_xxxxxx_add_default_to_lease_billings_amount.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lease_billings', function (Blueprint $table) {
            // Add default value to amount column
            $table->decimal('amount', 10, 2)->default(0)->change();

            // Also ensure total_amount exists if needed
            if (!Schema::hasColumn('lease_billings', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0)->after('amount');
            }
        });
    }

    public function down()
    {
        Schema::table('lease_billings', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->default(null)->change();
        });
    }
};
