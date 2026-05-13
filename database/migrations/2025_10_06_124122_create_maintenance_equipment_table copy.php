<?php
// database/migrations/2024_01_01_create_maintenance_equipment_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenance_equipment', function (Blueprint $table) {
            $table->id();
            $table->string('equipment_code')->unique();
            $table->string('name');
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->enum('type', [
                'splicing_machine',
                'otdr',
                'power_meter',
                'light_source',
                'fibre_identifier',
                'cleaning_tools',
                'test_equipment',
                'safety_equipment'
            ]);
            $table->enum('status', ['available', 'in_use', 'maintenance', 'retired']);
            $table->date('purchase_date')->nullable();
            $table->date('last_calibration')->nullable();
            $table->date('next_calibration')->nullable();
            $table->text('specifications')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_equipment');
    }
};
