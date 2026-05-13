<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_request_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_request_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->foreignId('changed_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Use shorter index names
            $table->index(['design_request_id', 'created_at'], 'drsh_design_request_id_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_request_status_histories');
    }
};
