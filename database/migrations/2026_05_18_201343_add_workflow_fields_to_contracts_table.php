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

            // expand workflow statuses
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

            // customer approval state
            $table->enum(
                'customer_approval_status',
                ['pending','approved','rejected']
            )
            ->default('pending')
            ->after('status');

            $table->timestamp('customer_rejected_at')
                ->nullable()
                ->after('customer_approved_at');

            $table->text('rejection_reason')
                ->nullable()
                ->after('customer_rejected_at');

            // admin approval
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('rejection_reason');

            $table->text('approval_notes')
                ->nullable()
                ->after('approved_by');

            // who initiated draft
            $table->foreignId('account_manager_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('quotation_id');
        });

        // migrate old statuses
        DB::table('contracts')
            ->where('status','pending_approval')
            ->update([
                'status'=>'customer_approved'
            ]);

        DB::table('contracts')
            ->where('status','sent_to_customer')
            ->update([
                'status'=>'sent'
            ]);

        // preserve admin approvals
        DB::table('contracts')
            ->whereNotNull('admin_approved_at')
            ->update([
                'approved_by'=>1
            ]);
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {

            $table->dropForeign(['approved_by']);
            $table->dropForeign(['account_manager_id']);

            $table->dropColumn([
                'customer_approval_status',
                'customer_rejected_at',
                'rejection_reason',
                'approved_by',
                'approval_notes',
                'account_manager_id'
            ]);
        });

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
    }
};
