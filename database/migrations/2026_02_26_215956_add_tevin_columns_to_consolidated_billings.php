<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consolidated_billings', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('consolidated_billings', 'tevin_status')) {
                $table->string('tevin_status')->nullable()->after('status');
            }

            if (!Schema::hasColumn('consolidated_billings', 'tevin_control_code')) {
                $table->string('tevin_control_code')->nullable()->after('tevin_status');
            }

            if (!Schema::hasColumn('consolidated_billings', 'tevin_qr_code')) {
                $table->text('tevin_qr_code')->nullable()->after('tevin_control_code');
            }

            if (!Schema::hasColumn('consolidated_billings', 'tevin_error_message')) {
                $table->text('tevin_error_message')->nullable()->after('tevin_qr_code');
            }

            if (!Schema::hasColumn('consolidated_billings', 'tevin_error_code')) {
                $table->string('tevin_error_code')->nullable()->after('tevin_error_message');
            }

            if (!Schema::hasColumn('consolidated_billings', 'kra_pin')) {
                $table->string('kra_pin')->nullable()->after('currency');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consolidated_billings', function (Blueprint $table) {
            $table->dropColumn([
                'tevin_status',
                'tevin_control_code',
                'tevin_qr_code',
                'tevin_error_message',
                'tevin_error_code',
                'kra_pin'
            ]);
        });
    }
};
