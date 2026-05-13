<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_document_requests_table.php
public function up()
{
    Schema::create('document_requests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('lease_id')->nullable()->constrained()->onDelete('cascade');
        $table->json('document_types'); // Array of requested document types
        $table->text('notes')->nullable();
        $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
        $table->text('admin_notes')->nullable();
        $table->timestamp('requested_at')->useCurrent();
        $table->timestamp('processed_at')->nullable();
        $table->foreignId('processed_by')->nullable()->constrained('users');
        $table->timestamps();

        $table->index(['user_id', 'status']);
        $table->index(['lease_id', 'status']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};
