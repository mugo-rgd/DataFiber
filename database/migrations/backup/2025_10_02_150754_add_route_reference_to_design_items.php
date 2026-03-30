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
     // Migration: add_route_reference_to_design_items
Schema::table('design_items', function (Blueprint $table) {
    $table->foreignId('survey_route_id')->nullable()->constrained()->onDelete('set null');
    $table->boolean('uses_surveyed_route')->default(false);
    $table->decimal('custom_distance_km', 8, 3)->nullable(); // If not using surveyed route
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('design_items', function (Blueprint $table) {
            //
        });
    }
};
