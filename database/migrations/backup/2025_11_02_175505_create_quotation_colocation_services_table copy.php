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
        Schema::create('quotation_colocation_services', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('quotation_id');
    $table->string('colocation_service_id', 20); // match varchar(20)
    $table->integer('quantity')->default(1);
    $table->integer('duration_months')->default(12);
    $table->decimal('unit_price', 10, 2);
    $table->decimal('total_price', 10, 2);
    $table->timestamps();

    // Foreign key on quotation
    $table->foreign('quotation_id')
        ->references('id')->on('quotations')
        ->onDelete('cascade');

    // Foreign key on colocation_list
    $table->foreign('colocation_service_id')
        ->references('service_id')->on('colocation_list')
        ->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_colocation_services');
    }
};
