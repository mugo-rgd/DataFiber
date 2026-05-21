<?php
// database/migrations/2024_xxxx_create_nfp_compliance_returns_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nfp_compliance_returns', function (Blueprint $table) {
            $table->id();

            // License Information
            $table->string('licensee_name');
            $table->string('license_no')->nullable();
            $table->string('other_licenses')->nullable();
            $table->string('financial_year');
            $table->string('quarter');

            // Address Information
            $table->json('physical_address')->nullable();
            $table->json('postal_address')->nullable();
            $table->json('contacts')->nullable();
            $table->boolean('address_changed')->default(false);

            // Infrastructure
            $table->json('infrastructure')->nullable();

            // Number Utilization
            $table->json('primary_numbers')->nullable();
            $table->json('secondary_numbers')->nullable();
            $table->json('bulk_sms')->nullable();

            // Broadband Infrastructure
            $table->json('broadband_infrastructure')->nullable();

            // Staff Data
            $table->json('staff_data')->nullable();

            // PWD Compliance
            $table->boolean('pwd_aware')->default(false);
            $table->boolean('pwd_complied')->default(false);
            $table->text('pwd_actions')->nullable();
            $table->text('pwd_challenges')->nullable();
            $table->text('pwd_future_plans')->nullable();

            // Environmental
            $table->text('ewaste_initiatives')->nullable();
            $table->text('carbon_initiatives')->nullable();
            $table->text('emca_status')->nullable();

            // Comments
            $table->text('comments')->nullable();

            // Submitter Information
            $table->string('submitter_name');
            $table->string('submitter_title')->nullable();
            $table->date('submitter_date');
            $table->string('company_stamp_path')->nullable();

            // Documents
            $table->json('documents')->nullable();

            // CAK Official Use
            $table->string('official_checked_by')->nullable();
            $table->string('official_checked_title')->nullable();
            $table->string('official_checked_signature')->nullable();
            $table->date('official_checked_date')->nullable();
            $table->string('official_verified_by')->nullable();
            $table->string('official_verified_title')->nullable();
            $table->string('official_verified_signature')->nullable();
            $table->date('official_verified_date')->nullable();
            $table->string('official_approved_by')->nullable();
            $table->string('official_approved_title')->nullable();
            $table->string('official_approved_signature')->nullable();
            $table->date('official_approved_date')->nullable();
            $table->enum('official_decision', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('official_remarks')->nullable();
            $table->string('official_stamp')->nullable();
            $table->string('compliance_id')->nullable();
            $table->string('tracking_code')->nullable();
            $table->string('certificate_number')->nullable();
            $table->date('certificate_valid_until')->nullable();

            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->foreignId('submitted_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfp_compliance_returns');
    }
};
