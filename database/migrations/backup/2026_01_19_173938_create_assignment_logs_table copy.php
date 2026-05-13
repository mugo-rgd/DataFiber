<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by_id')->constrained('users');
            $table->foreignId('assigned_to_id')->constrained('users');
            $table->string('assignment_type'); // 'regional', 'ict_regional', 'designer'
            $table->text('notes')->nullable();
            $table->string('priority')->default('normal');
            $table->timestamps();

            $table->index(['design_request_id', 'assignment_type']);
            $table->index('assigned_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_logs');
    }
};
