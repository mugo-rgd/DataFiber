<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_routes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('design_request_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('name_of_route');
            $table->string('region')->nullable();

            $table->enum('option', ['Non Premium', 'Premium', 'Metro'])
                ->default('Non Premium');

            $table->enum('tech_type', ['ADSS', 'OPGW', 'UG', 'OPGW/ADSS'])
                ->default('ADSS');

            $table->integer('fiber_cores')->nullable();
            $table->integer('no_of_cores_required')->default(1);

            $table->decimal('unit_cost_per_core_per_km_per_month', 10, 2)->default(0);
            $table->decimal('approx_distance_km', 10, 2)->default(0);
            $table->decimal('capital_expenditure', 15, 2)->default(0);

            $table->enum('currency', ['USD', 'KES'])->default('USD');
            $table->enum('availability', ['YES', 'NO'])->default('YES');

            $table->text('route_description')->nullable();
            $table->text('design_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_routes');
    }
};
