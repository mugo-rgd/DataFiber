<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->enum('customer_approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            $table->timestamp('customer_approved_at')->nullable()->after('customer_approval_status');
            $table->timestamp('customer_rejected_at')->nullable()->after('customer_approved_at');
            $table->text('rejection_reason')->nullable()->after('customer_rejected_at');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'customer_approval_status',
                'customer_approved_at',
                'customer_rejected_at',
                'rejection_reason'
            ]);
        });
    }
};
