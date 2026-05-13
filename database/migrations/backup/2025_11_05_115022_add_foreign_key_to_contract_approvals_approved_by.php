<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // First, check if there are any invalid user IDs and fix them
        $invalidRecords = DB::table('contract_approvals as ca')
            ->leftJoin('users as u', 'ca.approved_by', '=', 'u.id')
            ->whereNull('u.id')
            ->whereNotNull('ca.approved_by')
            ->select('ca.id', 'ca.approved_by')
            ->get();

        if ($invalidRecords->isNotEmpty()) {
            echo "Found {$invalidRecords->count()} invalid records. Fixing...\n";

            // Get a valid admin user ID
            $adminUser = DB::table('users')
                ->whereIn('role', ['admin', 'system_admin', 'accountmanager_admin', 'technical_admin'])
                ->orderBy('id')
                ->first();

            if ($adminUser) {
                DB::table('contract_approvals as ca')
                    ->leftJoin('users as u', 'ca.approved_by', '=', 'u.id')
                    ->whereNull('u.id')
                    ->whereNotNull('ca.approved_by')
                    ->update(['ca.approved_by' => $adminUser->id]);

                echo "Fixed invalid records using user ID: {$adminUser->id}\n";
            } else {
                // If no admin user found, delete the invalid records
                DB::table('contract_approvals as ca')
                    ->leftJoin('users as u', 'ca.approved_by', '=', 'u.id')
                    ->whereNull('u.id')
                    ->whereNotNull('ca.approved_by')
                    ->delete();

                echo "Deleted invalid records (no valid admin user found)\n";
            }
        }

        // Now add the foreign key constraint
        Schema::table('contract_approvals', function (Blueprint $table) {
            $table->foreign('approved_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        echo "Foreign key constraint added successfully!\n";
    }

    public function down()
    {
        Schema::table('contract_approvals', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
        });
    }
};
