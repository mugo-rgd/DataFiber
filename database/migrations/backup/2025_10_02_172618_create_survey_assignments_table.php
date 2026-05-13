<?php
// database/migrations/2024_01_15_000000_create_survey_assignments_table.php

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
        Schema::create('survey_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('surveyor_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_by_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('scheduled_at');
            $table->decimal('estimated_hours', 5, 2);
            $table->enum('priority', ['normal', 'high', 'urgent'])->default('normal');
            $table->text('requirements');
            $table->text('admin_notes')->nullable();
            $table->date('deadline')->nullable();
            $table->enum('status', ['assigned', 'in_progress', 'completed', 'cancelled'])->default('assigned');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('actual_hours', 5, 2)->nullable();
            $table->text('completion_notes')->nullable();
            $table->text('reassignment_reason')->nullable();
            $table->timestamp('reassigned_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['surveyor_id', 'scheduled_at']);
            $table->index(['status', 'priority']);
            $table->index(['design_request_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_assignments');
    }
};
