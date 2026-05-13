<?php
// database/migrations/2024_01_01_create_maintenance_requests_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('design_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('reported_by')->constrained('users');
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'critical']);
            $table->enum('status', ['open', 'assigned', 'in_progress', 'resolved', 'closed']);
            $table->enum('issue_type', [
                'fibre_cut',
                'equipment_failure',
                'signal_degradation',
                'power_issue',
                'environmental',
                'preventive_maintenance',
                'other'
            ]);
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->timestamp('reported_at');
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->integer('downtime_minutes')->default(0);
            $table->decimal('repair_cost', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_requests');
    }
};
