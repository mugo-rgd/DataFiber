<?php
// In database/migrations/xxxx_xx_xx_xxxxxx_add_simple_columns_to_lease_billings.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lease_billings', function (Blueprint $table) {
            // Add user_id without foreign key constraint first
            if (!Schema::hasColumn('lease_billings', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('lease_id');
            }

            // Add other missing columns
            if (!Schema::hasColumn('lease_billings', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0)->after('due_date');
            }

            if (!Schema::hasColumn('lease_billings', 'description')) {
                $table->text('description')->nullable()->after('total_amount');
            }

            // Fix status column - make it longer
            if (Schema::hasColumn('lease_billings', 'status')) {
                // We'll use DB statement to modify the column
                DB::statement("ALTER TABLE lease_billings MODIFY status VARCHAR(20) DEFAULT 'pending'");
            } else {
                $table->string('status', 20)->default('pending');
            }
        });
    }

    public function down()
    {
        Schema::table('lease_billings', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'total_amount', 'description']);
        });
    }
};
