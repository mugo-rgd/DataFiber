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
    Schema::create('revenue_forecasts', function (Blueprint $table) {
        $table->id();
        $table->date('forecast_date');
        $table->string('currency', 10);
        $table->decimal('actual_revenue', 18, 2)->default(0);
        $table->decimal('forecast_revenue', 18, 2)->default(0);
        $table->decimal('growth_rate_percent', 8, 2)->default(0);
        $table->string('forecast_method')->default('moving_average');
        $table->json('metadata')->nullable();
        $table->timestamps();

        $table->index(['forecast_date', 'currency']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenue_forecasts');
    }
};
