<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acceptance_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('design_requests')->onDelete('cascade');
            $table->string('certificate_ref')->unique();
            $table->string('to_company');
            $table->string('route_name');
            $table->string('link_name');
            $table->string('cable_type');
            $table->decimal('distance', 8, 3);
            $table->integer('cores_count');
            $table->date('effective_date');
            $table->string('lessor')->default('THE KENYA POWER & LIGHTING COMPANY PLC');
            $table->string('lessee');
            $table->string('lessee_address')->nullable();
            $table->string('lessee_contact')->nullable();

            // Witness 1
            $table->string('witness1_name');
            $table->date('witness1_date');
            $table->string('witness1_signature_path')->nullable();
            $table->string('witness1_stamp_path')->nullable();

            // Witness 2
            $table->string('witness2_name');
            $table->date('witness2_date');
            $table->string('witness2_signature_path')->nullable();
            $table->string('witness2_stamp_path')->nullable();

            // Witness 3
            $table->string('witness3_name');
            $table->date('witness3_date');
            $table->string('witness3_signature_path')->nullable();
            $table->string('witness3_stamp_path')->nullable();

            // Lessee 1
            $table->string('lessee1_name');
            $table->date('lessee1_date');
            $table->string('lessee1_signature_path')->nullable();
            $table->string('lessee1_stamp_path')->nullable();

            // Lessee 2
            $table->string('lessee2_name');
            $table->date('lessee2_date');
            $table->string('lessee2_signature_path')->nullable();
            $table->string('lessee2_stamp_path')->nullable();

            // Documents
            $table->string('test_report_path');
            $table->json('additional_documents_path')->nullable();

            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acceptance_certificates');
    }
};
