<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_request_rejections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_request_id')->constrained()->onDelete('cascade');
            $table->text('reason');
            $table->foreignId('rejected_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_request_rejections');
    }
};
