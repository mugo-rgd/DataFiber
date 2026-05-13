<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('body');
            $table->enum('type', ['text', 'image', 'file', 'system'])->default('text');
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('file_size')->nullable(); // Add file size
            $table->string('mime_type')->nullable(); // Add MIME type
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['conversation_id', 'created_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
