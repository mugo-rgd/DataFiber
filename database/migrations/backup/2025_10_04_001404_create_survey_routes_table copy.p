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
        Schema::create('survey_routes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('design_request_id')->constrained()->onDelete('cascade');
    $table->foreignId('surveyor_id')->constrained('users')->onDelete('cascade');
    $table->string('route_name');
    $table->text('route_description')->nullable();
    $table->decimal('total_distance', 8, 3)->default(0);
    $table->decimal('estimated_cost', 10, 2)->default(0);
    $table->enum('status', ['draft', 'in_progress', 'completed', 'approved'])->default('draft');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_routes');
    }
};
