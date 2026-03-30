<?php
// database/migrations/2024_01_15_000002_add_survey_fields_to_design_requests_table.php

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
        Schema::table('design_requests', function (Blueprint $table) {
            // Add survey-related columns
            $table->foreignId('surveyor_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('survey_status', [
                'not_required',
                'requested',
                'assigned',
                'in_progress',
                'completed',
                'failed',
                'cancelled'
            ])->default('not_required');

            $table->timestamp('survey_requested_at')->nullable();
            $table->timestamp('survey_scheduled_at')->nullable();
            $table->timestamp('survey_completed_at')->nullable();
            $table->text('survey_requirements')->nullable();
            $table->decimal('survey_estimated_hours', 5, 2)->nullable();
            $table->decimal('survey_actual_hours', 5, 2)->nullable();

            // Add indexes for better performance
            $table->index(['survey_status', 'surveyor_id']);
            $table->index('survey_scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('design_requests', function (Blueprint $table) {
            $table->dropForeign(['surveyor_id']);
            $table->dropColumn([
                'surveyor_id',
                'survey_status',
                'survey_requested_at',
                'survey_scheduled_at',
                'survey_completed_at',
                'survey_requirements',
                'survey_estimated_hours',
                'survey_actual_hours'
            ]);
        });
    }
};
