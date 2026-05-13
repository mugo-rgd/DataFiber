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
        // database/migrations/xxxx_create_conditional_certificates.php
Schema::create('conditional_certificates', function (Blueprint $table) {
    $table->id();
    $table->foreignId('request_id')->constrained('design_requests')->onDelete('cascade');
    $table->string('ref_number')->unique();
    $table->string('lessor');
    $table->string('lessee');
    $table->string('link_name');
    $table->string('otdr_serial');
    $table->date('calibration_date');
    $table->string('engineer_name');
    $table->date('certificate_date');
    $table->string('site_a');
    $table->string('site_b');
    $table->string('fibre_technology');
    $table->string('odf_connector_type');
    $table->decimal('total_length', 10, 3);
    $table->decimal('average_loss', 10, 2);
    $table->integer('splice_joints');
    $table->string('test_wavelength');
    $table->decimal('ior', 8, 4);
    $table->string('lessee_contact_name')->nullable();
    $table->date('lessee_date')->nullable();
    $table->string('lessee_designation')->nullable();
    $table->date('certificate_issue_date');
    $table->date('commissioning_end_date');
    $table->string('engineer_signature_path')->nullable();
    $table->string('inspection_report_path');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conditional_certificates');
    }
};
