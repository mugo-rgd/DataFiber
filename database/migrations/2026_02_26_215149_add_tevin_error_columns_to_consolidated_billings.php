<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('consolidated_billings', function (Blueprint $table) {
            // Add missing error columns
            if (!Schema::hasColumn('consolidated_billings', 'tevin_error_message')) {
                $table->text('tevin_error_message')->nullable()->after('tevin_response');
            }

            if (!Schema::hasColumn('consolidated_billings', 'tevin_error_code')) {
                $table->string('tevin_error_code')->nullable()->after('tevin_error_message');
            }

            // Ensure other TEVIN columns exist
            if (!Schema::hasColumn('consolidated_billings', 'tevin_status')) {
                $table->string('tevin_status')->nullable()->after('status');
            }

            if (!Schema::hasColumn('consolidated_billings', 'tevin_control_code')) {
                $table->string('tevin_control_code')->nullable()->after('tevin_status');
            }

            if (!Schema::hasColumn('consolidated_billings', 'tevin_qr_code')) {
                $table->text('tevin_qr_code')->nullable()->after('tevin_control_code');
            }

            if (!Schema::hasColumn('consolidated_billings', 'tevin_invoice_number')) {
                $table->string('tevin_invoice_number')->nullable()->after('tevin_qr_code');
            }

            if (!Schema::hasColumn('consolidated_billings', 'tevin_response')) {
                $table->json('tevin_response')->nullable()->after('tevin_invoice_number');
            }

            if (!Schema::hasColumn('consolidated_billings', 'tevin_submitted_at')) {
                $table->timestamp('tevin_submitted_at')->nullable()->after('tevin_response');
            }

            if (!Schema::hasColumn('consolidated_billings', 'tevin_committed_at')) {
                $table->timestamp('tevin_committed_at')->nullable()->after('tevin_submitted_at');
            }

            if (!Schema::hasColumn('consolidated_billings', 'tevin_submitted_by')) {
                $table->unsignedBigInteger('tevin_submitted_by')->nullable()->after('tevin_committed_at');
            }

            // Add kra_pin column if missing
            if (!Schema::hasColumn('consolidated_billings', 'kra_pin')) {
                $table->string('kra_pin')->nullable()->after('currency');
            }

            // Add kra_qr_code column if missing
            if (!Schema::hasColumn('consolidated_billings', 'kra_qr_code')) {
                $table->text('kra_qr_code')->nullable()->after('tevin_qr_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consolidated_billings', function (Blueprint $table) {
            $table->dropColumn([
                'tevin_error_message',
                'tevin_error_code',
                'tevin_status',
                'tevin_control_code',
                'tevin_qr_code',
                'tevin_invoice_number',
                'tevin_response',
                'tevin_submitted_at',
                'tevin_committed_at',
                'tevin_submitted_by',
                'kra_pin',
                'kra_qr_code'
            ]);
        });
    }
};
