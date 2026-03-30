<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fiber_networks', function (Blueprint $table) {
            $table->id();
            $table->string('network_id')->unique();
            $table->string('network_name');
            $table->string('region');
            $table->decimal('total_distance_km', 10, 2);
            $table->integer('fiber_cores');
            $table->enum('link_type', ['Metro', 'Premium', 'Non Premium']);
            $table->decimal('cost_per_month', 12, 2);
            $table->enum('currency', ['USD', 'KES'])->default('KES');
            $table->enum('status', ['Active', 'Damaged', 'Planned', 'Decommissioned'])->default('Active');
            $table->json('waypoints_json')->nullable();
            $table->text('connection_sequence')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fiber_networks');
    }
};
