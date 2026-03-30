<?php
// In database/migrations/xxxx_xx_xx_xxxxxx_fix_lease_billings_relationships.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lease_billings', function (Blueprint $table) {
            // First, check if customer_id column exists and needs to be changed to user_id
            if (Schema::hasColumn('lease_billings', 'customer_id')) {
                // If customer_id exists but we want to use user_id instead
                $table->dropForeign(['customer_id']);
                $table->renameColumn('customer_id', 'user_id');
            } else if (!Schema::hasColumn('lease_billings', 'user_id')) {
                // Add user_id if it doesn't exist
                $table->foreignId('user_id')->constrained()->after('lease_id');
            }

            // Add other missing columns if they don't exist
            if (!Schema::hasColumn('lease_billings', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0)->after('due_date');
            }

            if (!Schema::hasColumn('lease_billings', 'description')) {
                $table->text('description')->nullable()->after('total_amount');
            }

            // Fix status column if needed
            if (Schema::hasColumn('lease_billings', 'status')) {
                // Modify existing status column to be longer
                $table->string('status', 20)->default('pending')->change();
            } else {
                $table->string('status', 20)->default('pending');
            }
        });

        // Also fix leases table to use user_id instead of customer_id
        if (Schema::hasTable('leases')) {
            Schema::table('leases', function (Blueprint $table) {
                if (Schema::hasColumn('leases', 'customer_id')) {
                    $table->dropForeign(['customer_id']);
                    $table->renameColumn('customer_id', 'user_id');
                } else if (!Schema::hasColumn('leases', 'user_id')) {
                    $table->foreignId('user_id')->constrained()->after('id');
                }
            });
        }
    }

    public function down()
    {
        Schema::table('lease_billings', function (Blueprint $table) {
            // For rollback - revert to customer_id if needed
            if (Schema::hasColumn('lease_billings', 'user_id')) {
                $table->renameColumn('user_id', 'customer_id');
            }
        });

        if (Schema::hasTable('leases')) {
            Schema::table('leases', function (Blueprint $table) {
                if (Schema::hasColumn('leases', 'user_id')) {
                    $table->renameColumn('user_id', 'customer_id');
                }
            });
        }
    }
};
