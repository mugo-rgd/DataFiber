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
      Schema::create('compliance_certificates', function (Blueprint $table) {
    $table->id();

    $table->string('form_type'); // asp, csp, nfp
    $table->unsignedBigInteger('form_id');

    $table->string('certificate_no')->unique();
    $table->string('licensee_name');
    $table->string('license_no')->nullable();
    $table->string('financial_year');
    $table->string('quarter');

    $table->date('issue_date')->nullable();
    $table->date('expiry_date')->nullable();

    $table->string('certificate_path')->nullable();
    $table->string('status')->default('issued');

    $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();

    $table->timestamps();

    $table->index(['form_type', 'form_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_certificates');
    }
};
