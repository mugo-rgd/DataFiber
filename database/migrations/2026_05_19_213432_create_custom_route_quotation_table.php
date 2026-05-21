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
        Schema::create('custom_route_quotation', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
    $table->foreignId('custom_route_id')->constrained()->cascadeOnDelete();
    $table->decimal('monthly_cost', 15, 2)->default(0);
    $table->decimal('capital_expenditure', 15, 2)->default(0);
    $table->string('currency', 3)->default('USD');
    $table->timestamps();

    $table->unique(['quotation_id', 'custom_route_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_route_quotation');
    }
};
