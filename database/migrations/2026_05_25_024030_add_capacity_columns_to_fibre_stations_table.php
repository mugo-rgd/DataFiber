<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fibre_stations', function (Blueprint $table) {

            $table->integer('usedCores')
                ->default(0)
                ->after('darkFibreCores');

            $table->integer('availableCores')
                ->default(0)
                ->after('usedCores');

            $table->decimal('utilizationPercent',8,2)
                ->default(0)
                ->after('availableCores');

        });
    }

    public function down(): void
    {
        Schema::table('fibre_stations', function (Blueprint $table) {

            $table->dropColumn([
                'usedCores',
                'availableCores',
                'utilizationPercent'
            ]);

        });
    }
};
