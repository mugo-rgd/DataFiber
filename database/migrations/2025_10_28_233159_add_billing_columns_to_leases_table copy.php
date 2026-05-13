<?php
// In database/migrations/xxxx_xx_xx_xxxxxx_add_billing_columns_to_leases_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leases', function (Blueprint $table) {
            // Add the missing columns
            $table->date('next_billing_date')->nullable()->after('status');
            $table->timestamp('last_billed_at')->nullable()->after('next_billing_date');

            // If monthly_rent doesn't exist, add it too
            // if (!Schema::hasColumn('leases', 'monthly_rent')) {
            //     $table->decimal('monthly_rent', 10, 2)->default(0)->after('property_name');
            // }
        });
    }

    public function down()
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->dropColumn(['next_billing_date', 'last_billed_at']);
            // Only drop monthly_rent if we added it in this migration
            // if (Schema::hasColumn('leases', 'monthly_rent')) {
            //     $table->dropColumn('monthly_rent');
            // }
        });
    }
};
