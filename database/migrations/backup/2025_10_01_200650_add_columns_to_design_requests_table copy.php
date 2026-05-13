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
        Schema::table('design_requests', function (Blueprint $table) {
          $table->integer('cores_required')->nullable();
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('distance', 10, 2)->nullable();
            $table->integer('terms')->nullable();
            $table->string('technology_type')->nullable();
            $table->string('link_class')->nullable();
            $table->string('route_name')->nullable();
            $table->decimal('tax_rate', 5, 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('design_requests', function (Blueprint $table) {
            //
        });
    }
};
