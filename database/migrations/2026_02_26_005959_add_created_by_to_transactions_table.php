<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_created_by_to_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByToTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('status');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            }

            if (!Schema::hasColumn('transactions', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('created_by');
            }

            if (!Schema::hasColumn('transactions', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('completed_at');
            }

            if (!Schema::hasColumn('transactions', 'category')) {
                $table->string('category')->nullable()->after('payment_method');
            }

            if (!Schema::hasColumn('transactions', 'reference_number')) {
                $table->string('reference_number')->nullable()->after('category');
            }

            if (!Schema::hasColumn('transactions', 'notes')) {
                $table->text('notes')->nullable()->after('reference_number');
            }
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'created_by',
                'completed_at',
                'payment_method',
                'category',
                'reference_number',
                'notes'
            ]);
        });
    }
}
