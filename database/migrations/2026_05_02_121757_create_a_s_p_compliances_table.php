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
        Schema::create('asp_compliances', function (Blueprint $table) {
    $table->id();
    $table->string('licensee_name');
    $table->string('license_no')->nullable();
    $table->string('other_licenses')->nullable();
    $table->string('financial_year');
    $table->string('quarter');

    $table->json('form_data')->nullable();
    $table->json('attachments')->nullable();

    $table->string('status')->default('draft');
    $table->string('pdf_path')->nullable();
    $table->timestamp('submitted_at')->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamp('approved_at')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a_s_p_compliances');
    }
};
