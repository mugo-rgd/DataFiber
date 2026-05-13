<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('commercial_routes', function (Blueprint $table) {
            $table->id();
            $table->enum('option', ['OPTION 1', 'OPTION 2', 'OPTION 3']);
            $table->string('name_of_route');
            $table->integer('no_of_cores_required');
            $table->decimal('unit_cost_per_core_per_km_per_month', 10, 2);
            $table->decimal('approx_distance_km', 10, 2);
            $table->decimal('capital_expenditure', 15, 2)->default(0);
            $table->enum('availability', ['YES', 'NO']);
            $table->enum('currency', ['USD', 'KES']);
            $table->enum('tech_type', ['ADSS', 'OPGW', 'UG']);
            $table->timestamps();

            $table->index(['option', 'tech_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('commercial_routes');
    }
};
