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
       // In the migration file
Schema::create('assignment_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('design_request_id')->constrained()->cascadeOnDelete();
    $table->foreignId('assigned_by_id')->constrained('users');
    $table->foreignId('assigned_to_id')->constrained('users');
    $table->string('assignment_type'); // 'ict_regional', 'designer', etc.
    $table->text('notes')->nullable();
    $table->string('priority')->default('normal');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_logs');
    }
};
