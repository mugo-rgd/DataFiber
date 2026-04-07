<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->integer('unread_count')->default(0);
            $table->boolean('is_muted')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->timestamp('last_seen_at')->nullable();
            $table->unique(['user_id', 'conversation_id']);
           
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_user');
    }
};
