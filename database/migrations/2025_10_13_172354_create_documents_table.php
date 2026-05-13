<?php
// database/migrations/2024_01_01_000002_create_documents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->boolean('has_expiry')->default(false);

            // New fields for document management
            $table->string('document_type')->nullable()->comment('kra_pin_certificate, business_registration_certificate, id_copy, other');
            $table->string('file_path')->nullable()->comment('Only populated when a user uploads a file');
            $table->string('file_name')->nullable()->comment('Only populated when a user uploads a file');
            $table->unsignedBigInteger('uploaded_by')->nullable()->comment('User who uploaded the file');
            $table->enum('status', ['pending_review', 'approved', 'rejected', 'expired'])->default('pending_review');
            $table->string('mime_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->boolean('is_required')->default(false);
            $table->text('description')->nullable();

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('uploaded_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
