<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conditional_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('ref_number')->unique();
            $table->string('lessor');
            $table->string('lessee');
            $table->string('link_name');
            $table->string('otdr_serial');
            $table->date('calibration_date');
            $table->string('site_a');
            $table->string('site_b');
            $table->string('fibre_technology');
            $table->string('odf_connector_type');
            $table->decimal('total_fibre_length', 8, 2);
            $table->decimal('average_link_loss', 5, 2);
            $table->integer('splice_joints');
            $table->integer('test_wavelength');
            $table->decimal('ior', 8, 4);
            $table->string('engineer_name');
            $table->date('certificate_date');
            $table->date('commissioning_end_date');
            $table->string('inspection_report_path')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('stamp_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conditional_certificates');
    }
};
