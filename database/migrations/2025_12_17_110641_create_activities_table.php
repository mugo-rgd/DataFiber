<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable()->index(); // For categorizing logs
            $table->text('description'); // Activity description
            $table->nullableMorphs('subject'); // The model the activity is performed on
            $table->nullableMorphs('causer'); // Who caused the activity (usually user)
            $table->json('properties')->nullable(); // Additional data
            $table->string('event')->nullable(); // e.g., created, updated, deleted
            $table->string('batch_uuid')->nullable()->index(); // For grouping related activities
            $table->timestamps();

            // Indexes for better performance
            $table->index(['subject_id', 'subject_type']);
            $table->index(['causer_id', 'causer_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
