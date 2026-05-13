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
        Schema::table('leases', function (Blueprint $table) {
            $table->string('test_report_path')->nullable()->after('attachments');
            $table->string('test_report_type')->nullable()->after('test_report_path');
            $table->date('test_date')->nullable()->after('test_report_type');
            $table->text('test_report_description')->nullable()->after('test_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->dropColumn([
                'test_report_path',
                'test_report_type',
                'test_date',
                'test_report_description'
            ]);
        });
    }
};
