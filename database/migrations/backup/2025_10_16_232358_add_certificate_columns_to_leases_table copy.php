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
            $table->string('acceptance_certificate_path')->nullable()->after('test_report_description');
            $table->timestamp('acceptance_certificate_generated_at')->nullable()->after('acceptance_certificate_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->dropColumn(['acceptance_certificate_path', 'acceptance_certificate_generated_at']);
        });
    }
};
