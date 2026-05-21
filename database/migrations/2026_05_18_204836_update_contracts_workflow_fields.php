<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('contracts', 'account_manager_id')) {
                $table->unsignedBigInteger('account_manager_id')->nullable()->after('quotation_id');
            }

            if (!Schema::hasColumn('contracts', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('account_manager_id');
            }

            if (!Schema::hasColumn('contracts', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('contracts', 'customer_approval_status')) {
                $table->enum('customer_approval_status', ['pending', 'approved', 'rejected'])
                    ->default('pending')
                    ->after('sent_at');
            }

            if (!Schema::hasColumn('contracts', 'customer_rejected_at')) {
                $table->timestamp('customer_rejected_at')->nullable()->after('customer_approved_at');
            }

            if (!Schema::hasColumn('contracts', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('customer_rejected_at');
            }

            if (!Schema::hasColumn('contracts', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('rejection_reason');
            }

            if (!Schema::hasColumn('contracts', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            if (!Schema::hasColumn('contracts', 'approval_notes')) {
                $table->text('approval_notes')->nullable()->after('approved_at');
            }
        });

        DB::statement("
            ALTER TABLE contracts
            MODIFY status ENUM(
                'draft',
                'sent',
                'customer_approved',
                'customer_rejected',
                'approved',
                'rejected',
                'active'
            ) DEFAULT 'draft'
        ");

        DB::table('contracts')
            ->where('status', 'sent_to_customer')
            ->update(['status' => 'sent']);

        DB::table('contracts')
            ->where('status', 'pending_approval')
            ->update([
                'status' => 'customer_approved',
                'customer_approval_status' => 'approved'
            ]);

        DB::table('contracts')
            ->where('status', 'approved')
            ->whereNotNull('admin_approved_at')
            ->update([
                'approved_at' => DB::raw('admin_approved_at')
            ]);
    }

    public function down(): void
    {
        DB::table('contracts')
            ->where('status', 'sent')
            ->update(['status' => 'sent_to_customer']);

        DB::table('contracts')
            ->where('status', 'customer_approved')
            ->update(['status' => 'pending_approval']);

        DB::statement("
            ALTER TABLE contracts
            MODIFY status ENUM(
                'draft',
                'pending_approval',
                'sent_to_customer',
                'approved',
                'rejected'
            ) DEFAULT 'draft'
        ");

        Schema::table('contracts', function (Blueprint $table) {
            $columns = [
                'account_manager_id',
                'customer_id',
                'sent_at',
                'customer_approval_status',
                'customer_rejected_at',
                'rejection_reason',
                'approved_by',
                'approved_at',
                'approval_notes',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('contracts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
