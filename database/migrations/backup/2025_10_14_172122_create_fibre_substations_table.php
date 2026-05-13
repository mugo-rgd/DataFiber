<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up()
    {
        Schema::create('fibre_stations', function (Blueprint $table) {
            $table->id();

            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);
            $table->string('name');
            $table->string('capacity', 50)->nullable();

            $table->enum('fibreStatus', ['Available', 'Unavailable', 'Under Maintenance'])->default('Available');
            $table->integer('darkFibreCores')->default(12);
            $table->enum('connectionType', ['Patch Panel', 'Direct Tap', 'Splice Point'])->default('Patch Panel');
            $table->enum('owner', ['KPLC', 'KETRACO', 'KENGEN', 'REA', 'PRIVATE'])->default('KPLC');

            $table->string('area', 100)->nullable();
            $table->string('location')->nullable();

            $table->timestamps(); // creates created_at and updated_at columns

            // Indexes
            $table->index(['lat', 'lng'], 'idx_lat_lng');
            $table->index('area', 'idx_area');
            $table->index('owner', 'idx_owner');
            $table->index('fibreStatus', 'idx_fibre_status');
            $table->index('connectionType', 'idx_connection_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fibre_stations');
    }
};
