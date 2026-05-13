<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            // Approval columns
            $table->timestamp('approved_at')->nullable()->after('sent_at');
            $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->onDelete('set null');
            $table->text('approval_notes')->nullable()->after('approved_by');

            // Rejection columns
            // $table->timestamp('rejected_at')->nullable()->after('approval_notes');
            $table->foreignId('rejected_by')->nullable()->after('rejected_at')->constrained('users')->onDelete('set null');
            $table->text('rejection_notes')->nullable()->after('rejected_by');

            // Remove old columns if they exist with different names
            // $table->dropColumn(['accepted_at', 'rejected_at']); // Uncomment if you have old columns
        });
    }

    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'approved_at',
                'approved_by',
                'approval_notes',
                // 'rejected_at',
                'rejected_by',
                'rejection_notes'
            ]);
        });
    }
};
