<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_missing_columns_to_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Add currency column if it doesn't exist
            if (!Schema::hasColumn('transactions', 'currency')) {
                $table->string('currency', 3)->default('KSH')->after('amount');
            }

            // Add created_by column if it doesn't exist
            if (!Schema::hasColumn('transactions', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('status');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            }

            // Add payment_method column if it doesn't exist
            if (!Schema::hasColumn('transactions', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('created_by');
            }

            // Add category column if it doesn't exist
            if (!Schema::hasColumn('transactions', 'category')) {
                $table->string('category')->nullable()->after('payment_method');
            }

            // Add reference_number column if it doesn't exist
            if (!Schema::hasColumn('transactions', 'reference_number')) {
                $table->string('reference_number')->nullable()->after('category');
            }

            // Add notes column if it doesn't exist
            if (!Schema::hasColumn('transactions', 'notes')) {
                $table->text('notes')->nullable()->after('reference_number');
            }

            // Add completed_at column if it doesn't exist
            if (!Schema::hasColumn('transactions', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('notes');
            }
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $columns = ['currency', 'payment_method', 'category', 'reference_number', 'notes', 'completed_at'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('transactions', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('transactions', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });
    }
}
