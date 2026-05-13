<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('last_read_at')->nullable();
            $table->enum('role', ['member', 'admin'])->default('member');
            $table->boolean('is_muted')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->unique(['conversation_id', 'user_id']);

            // Add indexes for better performance
            $table->index(['user_id', 'last_read_at']);
            $table->index('conversation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
