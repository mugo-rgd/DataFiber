<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MatanYadaev\EloquentSpatial\Enums\Srid;

return new class extends Migration
{
    public function up()
    {
        // Add spatial columns to fiber_nodes
        Schema::table('fiber_nodes', function (Blueprint $table) {
            $table->point('location', Srid::WGS84->value)->nullable();
            $table->spatialIndex('location');
        });

        // Add spatial columns to fiber_networks
        Schema::table('fiber_networks', function (Blueprint $table) {
            $table->linestring('geometry', Srid::WGS84->value)->nullable();
            $table->spatialIndex('geometry');
        });

        // Add spatial columns to fiber_segments
        Schema::table('fiber_segments', function (Blueprint $table) {
            $table->linestring('geometry', Srid::WGS84->value)->nullable();
            $table->spatialIndex('geometry');
        });
    }

    public function down()
    {
        Schema::table('fiber_nodes', function (Blueprint $table) {
            $table->dropSpatialIndex(['location']);
            $table->dropColumn('location');
        });

        Schema::table('fiber_networks', function (Blueprint $table) {
            $table->dropSpatialIndex(['geometry']);
            $table->dropColumn('geometry');
        });

        Schema::table('fiber_segments', function (Blueprint $table) {
            $table->dropSpatialIndex(['geometry']);
            $table->dropColumn('geometry');
        });
    }
};
