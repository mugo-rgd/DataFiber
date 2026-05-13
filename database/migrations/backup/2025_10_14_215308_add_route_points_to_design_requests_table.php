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
            $table->json('route_points')->nullable()->after('technical_requirements');
            $table->decimal('total_distance', 10, 2)->nullable()->after('route_points');
            $table->integer('point_count')->default(0)->after('total_distance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('design_requests', function (Blueprint $table) {
            $table->dropColumn(['route_points', 'total_distance', 'point_count']);
        });
    }
};
