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
    Schema::create('executive_insights', function (Blueprint $table) {
        $table->id();
        $table->date('snapshot_date');
        $table->string('category')->nullable();
        $table->string('severity')->default('info');
        $table->string('title');
        $table->text('message');
        $table->decimal('value', 18, 2)->nullable();
        $table->string('currency')->nullable();
        $table->json('metadata')->nullable();
        $table->timestamps();

        $table->index(['snapshot_date', 'category']);
        $table->index(['severity']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('executive_insights');
    }
};
