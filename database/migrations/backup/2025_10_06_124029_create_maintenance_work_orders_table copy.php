<?php
// database/migrations/2024_01_01_create_maintenance_work_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenance_work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('work_order_number')->unique();
            $table->foreignId('maintenance_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_technician')->constrained('users');
            $table->foreignId('survey_route_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('route_segment_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('work_description');
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed', 'cancelled']);
            $table->enum('work_type', [
                'emergency_repair',
                'scheduled_maintenance',
                'fibre_splicing',
                'equipment_replacement',
                'testing',
                'inspection',
                'upgrade'
            ]);
            $table->timestamp('scheduled_start')->nullable();
            $table->timestamp('scheduled_end')->nullable();
            $table->timestamp('actual_start')->nullable();
            $table->timestamp('actual_end')->nullable();
            $table->integer('estimated_duration_minutes')->nullable();
            $table->integer('actual_duration_minutes')->nullable();
            $table->text('work_performed')->nullable();
            $table->text('materials_used')->nullable();
            $table->decimal('labor_cost', 10, 2)->nullable();
            $table->decimal('material_cost', 10, 2)->nullable();
            $table->text('technician_notes')->nullable();
            $table->text('customer_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_work_orders');
    }
};
