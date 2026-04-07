<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fiber_segments', function (Blueprint $table) {
            $table->id();
            $table->string('segment_id')->unique();
            $table->string('network_id');
            $table->integer('segment_order');
            $table->string('source_name');
            $table->decimal('source_lat', 10, 7);
            $table->decimal('source_lon', 10, 7);
            $table->string('destination_name');
            $table->decimal('dest_lat', 10, 7);
            $table->decimal('dest_lon', 10, 7);
            $table->string('cable_type');
            $table->decimal('distance_km', 10, 2);
            $table->integer('fiber_cores');
            $table->enum('link_type', ['Metro', 'Premium', 'Non Premium']);
            $table->decimal('cost_per_month', 12, 2);
            $table->enum('currency', ['USD', 'KES'])->default('KES');
            $table->enum('status', ['Active', 'Damaged', 'Planned', 'Decommissioned'])->default('Active');
            $table->foreign('network_id')->references('network_id')->on('fiber_networks')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fiber_segments');
    }
};
