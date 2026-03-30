<?php

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
       // Migration: create_route_segments_table
Schema::create('route_segments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('survey_route_id')->constrained()->onDelete('cascade');
    $table->integer('segment_number');
    $table->string('segment_name');

    // Segment Details
    $table->enum('installation_type', ['aerial', 'underground', 'conduit', 'direct_burial']);
    $table->decimal('distance_km', 6, 3);
    $table->string('terrain_type');
    $table->enum('complexity', ['low', 'medium', 'high']);

    // Infrastructure
    $table->integer('pole_count')->default(0);
    $table->integer('manhole_count')->default(0);
    $table->integer('splice_count')->default(0);

    // Obstacles & Challenges
    $table->json('obstacles')->nullable(); // [{"type": "river", "width": "50m"}, ...]
    $table->text('challenges')->nullable();
    $table->decimal('cost_multiplier', 3, 2)->default(1.0);

    // GPS Coordinates
    $table->decimal('start_lat', 10, 6)->nullable();
    $table->decimal('start_lng', 10, 6)->nullable();
    $table->decimal('end_lat', 10, 6)->nullable();
    $table->decimal('end_lng', 10, 6)->nullable();

    $table->timestamps();

    $table->index(['survey_route_id', 'segment_number']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes_segments');
    }
};
