<?php
// In database/migrations/xxxx_xx_xx_xxxxxx_add_missing_columns_to_lease_billings.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lease_billings', function (Blueprint $table) {
            // Add missing columns
            if (!Schema::hasColumn('lease_billings', 'customer_id')) {
                $table->foreignId('customer_id')->constrained()->after('lease_id');
            }

            if (!Schema::hasColumn('lease_billings', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0)->after('due_date');
            }

            if (!Schema::hasColumn('lease_billings', 'description')) {
                $table->text('description')->nullable()->after('total_amount');
            }

            // Make sure status column exists and is correct
            if (!Schema::hasColumn('lease_billings', 'status')) {
                $table->string('status', 20)->default('pending');
            }
        });
    }

    public function down()
    {
        Schema::table('lease_billings', function (Blueprint $table) {
            $table->dropColumn(['customer_id', 'total_amount', 'description']);
        });
    }
};
