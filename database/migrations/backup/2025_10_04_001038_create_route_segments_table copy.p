<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('route_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_route_id')->constrained()->onDelete('cascade');
            $table->integer('segment_number');
            $table->string('segment_name');
            $table->enum('installation_type', ['aerial', 'underground', 'conduit', 'direct_burial']);
            $table->decimal('distance_km', 6, 3);
            $table->string('terrain_type');
            $table->enum('complexity', ['low', 'medium', 'high']);
            $table->integer('pole_count')->default(0);
            $table->integer('manhole_count')->default(0);
            $table->integer('splice_count')->default(0);
            $table->json('obstacles')->nullable();
            $table->text('challenges')->nullable();
            $table->decimal('cost_multiplier', 3, 2)->default(1.00);
            $table->decimal('start_lat', 10, 6)->nullable();
            $table->decimal('start_lng', 10, 6)->nullable();
            $table->decimal('end_lat', 10, 6)->nullable();
            $table->decimal('end_lng', 10, 6)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('route_segments');
    }
};
